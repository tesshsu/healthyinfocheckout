<?php
/**
 * 2007-2023 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2023 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Class HealthyInfoCheckOut
 */
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
require_once _PS_MODULE_DIR_ . 'healthyinfocheckout/class/healthyInfoCheckoutModel.php';

class HealthyInfoCheckOut extends Module implements WidgetInterface
{
    protected $config_form = false;

    const PREFIX = 'ps_';

    /** @var string */
    const CLIENT_ID = 'client_id';

    /** @var string */
    const CLIENT_SECRET = 'client_secret';

    /** @var string $_html */
    private $_html = '';

    /** @var array $_postErrors */
    private $_postErrors = array();

    /** @var string $header */
    protected $header;

    /** @var string $BASE_ENDPOINT */
    protected $BASE_ENDPOINT;

    /** @var string $LOGIN_ENDPOINT */
    protected $LOGIN_ENDPOINT;

    /** @var string $authToken */
    protected $authToken = NULL;

    /** @var string $messageService */
    private $messageService;

    /** @var string $authError */
    private $authError;
    private $templateFile;


    public function __construct()
    {
        $this->name = 'healthyinfocheckout';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Tess';
        $this->need_instance = 0;
        $this->bootstrap = TRUE;
        parent::__construct();


        $this->displayName = $this->l('HealthyQ');
        $this->description = $this->l('Adds extra questions about health insurance and prescription to the checkout page');

        $this->confirmUninstall = $this->l('Voulez-vous vraiment désinstaller ?', $this->name);

        $this->header = ['Content-Type: application/json', 'Accept: application/json', 'User-Agent:' . $_SERVER['HTTP_USER_AGENT']];

        $this->ps_versions_compliancy = [
            'min' => '1.6.5.0',
            'max' => _PS_VERSION_,
        ];
        $this->BASE_ENDPOINT = 'https://dev-opzbl59r.auth0.com/';
        $this->LOGIN_ENDPOINT = $this->BASE_ENDPOINT . 'oauth/token';
        $this->messageService = "Si vous n'avez pas accès et clé secrète, veuillez contacter le service client";
        $this->authError = 'Veuillez vérifier vos clés d\'identification';
        $this->templateFile = 'module:healthyinfocheckout/views/templates/front/checkout/_partials/personal-information.tpl';
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install()
            && (bool) $this->registerHook('displayPersonalInformationTop')
            && $this->createTable();
    }

    public function uninstall()
    {
        if (parent::uninstall()) {
                if (!$this->unregisterHook('displayPersonalInformationTop')) {
                    return FALSE;
                }
            return TRUE;
        }
        // If uninstall delete table
        $this->deleteTable();
        $this->log('Uninstall', 'info');
        return FALSE;
    }
    /**
     * @return bool
     */
    public function setConfigurationValues()
    {
        // Auth account setting
        Configuration::updateValue('client_id', '');
        Configuration::updateValue('client_secret', '');
        Configuration::updateValue('audience', 'https://dev-opzbl59r.auth0.com/api/v2/');
        Configuration::updateValue('grant_type', 'client_credentials');
        return true;
    }
    public function login()
    {
        $accessKey = Configuration::get('client_id');
        $secretKey = Configuration::get('client_secret');
        $audience = 'https://dev-opzbl59r.auth0.com/api/v2/';
        $grant_type = 'client_credentials';

        if ($this->authToken == NULL && $accessKey != "" && $secretKey != "") {
            $data['client_id'] = $accessKey;
            $data['client_secret'] = $secretKey;
            $data['audience'] = $audience;
            $data['grant_type'] = $grant_type ;
            $this->log('$data :' . json_encode($data), 'info');
            $loginData = $this->callApi("POST", $this->LOGIN_ENDPOINT, $this->header, json_encode($data));
            $response = json_decode($loginData['data'], true); // decode the JSON string
            $this->log('$loginData :' . json_encode($loginData), 'info');

            // Verify post return login as auth true
            if ($accessKey && $secretKey && $response['access_token']) {
                $this->log('Pass auth0', 'info');
                return true;
            }
        }
        return false;
    }
    private function callApi($method, $url, $header, $data = false)
    {
        $returnArray['error'] = 1;
        if ($this->curl_installed()) {
            $handle = curl_init($url);
            switch ($method)
            {
                case "POST":
                    curl_setopt($handle, CURLOPT_POST, 1);

                    if ($data)
                        curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
                    break;
                case "PUT":
                    curl_setopt($handle, CURLOPT_PUT, 1);
                    break;
                default:
                    if ($data)
                        $url = sprintf("%s?%s", $url, http_build_query($data));
            }
            curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_HTTPHEADER, $header);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($handle);
            $httpcode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
            $err = curl_error($handle);
            curl_close($handle);
            if ($err) {
                $returnArray['error'] = 1;
                $returnArray['data'] = $err;
                $returnArray['response'] = $response;
                echo "cURL Error #:" . $err;
            } else {
                if ($httpcode == '200') {
                    $returnArray['error'] = 0;
                    $returnArray['data'] = $response;
                }
            }
        }
        return $returnArray;
    }
    private static function curl_installed()
    {
        return function_exists('curl_version');
    }
    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $this->_html = '<h2>' . $this->l("Health Info Check Out") . '</h2>';
        $this->displayForm();
        if (!empty($_POST) and Tools::isSubmit('submitSave')) {
            $this->_postValidation();
            if (!sizeof($this->_postErrors)){
                $this->log('Post validation submit', 'info');
                $this->authToken = NULL;
                $this->_postProcess();
            }
            else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        }
        return $this->_html;
    }
    private function displayForm()
    {
        $fieldsValue = [
            self::CLIENT_ID => Tools::getValue(
                self::CLIENT_ID ,
                Configuration::get(self::CLIENT_ID )
            ),
            self::CLIENT_SECRET => Tools::getValue(
                self::CLIENT_SECRET,
                Configuration::get(self::CLIENT_SECRET)
            ),
        ];
        // Init Fields form array
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('client id'),
                        'name' => self::CLIENT_ID,
                        'size' => 100,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('client secret'),
                        'name' => self::CLIENT_SECRET,
                        'size' => 100,
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = [];
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSave';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name
            . '&tab_module=' . $this->tab
            . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'fields_value' => $fieldsValue,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];
        $this->_html .= $helper->generateForm([$form]);
    }
    private function _postValidation()
    {
        // Check configuration values
        $client_id = Tools::getValue('client_id');
        $client_secret = Tools::getValue('client_secret');
        $this->log($client_id, 'info');
        $this->log($client_secret, 'info');

        if (Tools::getValue('client_id') == '' || Tools::getValue('client_secret') == '') {
            $this->_postErrors[] = $this->l($this->messageService);
        }
    }
    private function _postProcess()
    {
        // Saving new configurations and Check authentication
        if (Configuration::updateValue(self::CLIENT_ID, Tools::getValue(self::CLIENT_ID))
            && Configuration::updateValue(self::CLIENT_SECRET, Tools::getValue(self::CLIENT_SECRET)) && $this->login()) {
            $this->_html .= $this->displayConfirmation($this->l('La configuration est terminée'));
        }
        else {
            $this->_html .= $this->displayError($this->l($this->authError));
        }
    }

    public function renderWidget($hookName, array $configuration)
    {
        $this->log('render get widget', 'info');
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->fetch($this->templateFile, $this->getCacheId('healthyinfocheckout'));
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        $this->login();
        $this->header[] = "Authorization: Bearer " . $this->authToken;
        // Then we integrate data send to ps_customer note
        $customerId = $this->context->customer->id;
        $customer = new Customer($customerId);
        $message = null;
        // By default has no insurance and prescription
        $text_has_insurance = 'client dispose d\'une assurance santé';
        $text_has_prescription = 'client dispose d\'une ordonnance médicale';
        $has_insurance = 0;
        $has_prescription = 0;
        $this->log('render widget variable', 'info');
        if ($customer->note != null) {
            $this->log('submitSave', 'info');
            $has_insurance = Tools::getValue('has_insurance') == false ? "0" : "1";
            $has_prescription = Tools::getValue('has_insurance') == false ? "0" : "1";
            $extra_note = Tools::getValue('extra_note');
            Configuration::updateValue('has_insurance', $has_insurance, false);
            Configuration::updateValue('has_prescription', $has_prescription, false);
            configuration::updateValue('extra_note', $extra_note, false);

            $this->log('$$has_insurance :' . $has_insurance, 'info');
            $this->log('$has_prescription :' . $has_prescription, 'info');
            // Update customer data in database
            if($has_insurance == 1 && $has_prescription == 1){
                $customer->note = $text_has_insurance . ' et aussi ' . $text_has_prescription;
            } elseif ($has_insurance == 1) {
                $customer->note = $text_has_insurance;
            } elseif ($has_prescription == 1) {
                $customer->note = $text_has_prescription;
            }
            $this->log('customer note from base php :' . $customer->note, 'info');
            if($customer->note && tools::isSubmit('extra_note')){
                $customer->update();
                // Insert into table ps_healthy_info_checkout
                $healthyInfoCheckout = new healthyInfoCheckoutModel();
                $healthyInfoCheckout->id_customer = $customerId;
                $healthyInfoCheckout->has_insurance = $has_insurance;
                $healthyInfoCheckout->has_prescription = $has_prescription;
                $healthyInfoCheckout->extra_note = $extra_note;
                $healthyInfoCheckout->created_at = date('Y-m-d H:i:s');
                $healthyInfoCheckout->save();
                $this->log('save table healthinfocheckout info :' . $healthyInfoCheckout->id, 'info');
                $message = "Success valide";
            }
        }

        return array(
            'message' => $message,
            'has_insurance' => Configuration::get('has_insurance'),
            'has_prescription' => Configuration::get('has_prescription'),
            'extra_note' => Configuration::get('extra_note'),
        );

        /*$this->smarty->assign([
            'message' => $message,
            'has_insurance' => Configuration::get('has_insurance'),
            'has_prescription' => Configuration::get('has_prescription'),
            'select_healthy_option_url' => $this->context->link->getModuleLink(
                $this->name,
                'action',
                ['id_customer' => $customerId, 'has_insurance' => $has_insurance, 'has_prescription' => $has_prescription]
            ),
        ]);*/
    }

    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "healthy_info_checkout` (
            `id_healthy_info_checkout` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `id_customer` int(10) unsigned NOT NULL,
            `has_insurance` BOOLEAN NOT NULL DEFAULT 0,
            `has_prescription` BOOLEAN NOT NULL DEFAULT 0,
            `extra_note` TEXT NULL,
            `created_at` datetime NOT NULL,
            PRIMARY KEY (`id_healthy_info_checkout`)
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";
        return Db::getInstance()->execute($sql);
    }

    private function deleteTable()
    {
        $sql = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "healthy_info_checkout`";
        return Db::getInstance()->execute($sql);
    }
    // Do not removed until to publish add on store will stock as abstract class show log inner prestashop logger
    const DEFAULT_LOG_FILE = 'dev2.log';
    public static function log($message, $level = 'debug', $fileName = null)
    {
        $fileDir = _PS_ROOT_DIR_ . '/var/logs/';

        if (!$fileName)
            $fileName = self::DEFAULT_LOG_FILE;

        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }

        $formatted_message = '*' . $level . '* ' . " -- " . date('Y/m/d - H:i:s') . ': ' . $message . "\r\n";

        return file_put_contents($fileDir . $fileName, $formatted_message, FILE_APPEND);
    }
}
