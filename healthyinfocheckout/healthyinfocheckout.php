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
class HealthyInfoCheckOut extends Module
{
    protected $config_form = false;

    const PREFIX = 'ps_';

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

    /** @var array $_hooks */
    private $_hooks;

    /** @var string $authToken */
    protected $authToken = NULL;

    /** @var string $messageService */
    private $messageService;

    /** @var string $authError */
    private $authError;

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

        $this->_hooks = array('header',
            'displayPersonalInformationTop',
            'displayCheckoutForm', 'displayOrderConfirmation',
            'displayAdminOrder', 'actionOrderStatusPostUpdate',
            'displayBeforeCarrier',
            'actionOrderStatusUpdate',
            'actionValidateOrder',
            'actionPaymentConfirmation',
            'actionValidateOrderAfter',
            'displayFeatureForm',
            'displayNewsletterRegistration');
        $this->BASE_ENDPOINT = 'https://dev-opzbl59r.auth0.com/';
        $this->LOGIN_ENDPOINT = $this->BASE_ENDPOINT . 'oauth/token';
        $this->messageService = "Si vous n'avez pas accès et clé secrète, veuillez contacter le service client";
        $this->authError = 'Veuillez vérifier vos clés d\'identification';
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (extension_loaded('curl') == false) {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }

        if (parent::install()) {
            foreach ($this->_hooks as $hook) {
                if (!$this->registerHook($hook)) {
                    return FALSE;
                }
            }
            // If install set setting configuration
            $this->setConfigurationValues();

            return TRUE;
        }
        return FALSE;
    }

    public function uninstall()
    {
        if (parent::uninstall()) {
            foreach ($this->_hooks as $hook) {
                if (!$this->unregisterHook($hook)) {
                    return FALSE;
                }
            }
            return TRUE;
        }
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



        return true;
    }
    public function login()
    {
        $accessKey = Configuration::get('client_id');
        $secretKey = Configuration::get('client_secret');
        $audience = Configuration::get('audience');
        $grant_type = Configuration::get('client_credentials');

        if ($this->authToken == NULL && $accessKey != "" && $secretKey != "") {
            $data['accessKey'] = $accessKey;
            $data['secretKey'] = $secretKey;
            $data['audience'] = $audience;
            $data['grant_type'] = $grant_type ;

            $loginData = $this->callApi("POST",$this->LOGIN_ENDPOINT, $this->header, json_encode($data));
            $this->log('$loginData :' . $loginData, 'info');

            // Verify post return login as auth true
            if ($accessKey && $secretKey) {
                $this->authToken = $loginData['access_token'];
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

        if (!empty($_POST) and Tools::isSubmit('submitSave')) {
            $this->_postValidation();
            if (!sizeof($this->_postErrors)){
                $this->authToken = NULL;
                $this->_postProcess();
            }
            else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        }
        $this->_displayForm();
        return $this->_html;
    }
    private function _displayForm()
    {
        $this->_html .= '<fieldset>';
        $alert = array();
        if (!Configuration::get('client_id') || Configuration::get('client_id') == '') $alert['client_id'] = 1;
        if (!Configuration::get('client_secret') || Configuration::get('client_secret') == '') $alert['client_secret'] = 1;
        if (!count($alert)) $this->_html .= '<strong>' . $this->l("HealthyO est configuré, vous pouvez desormais d'avoir information du client concerant sante sur command page onformations personnelles") . '</strong>';
        else {
            $this->_html .=  $this->l("HealthyO n'est pas encore configuré, veuillez renseigner votre clé d'accès (client_id) et votre clé secrète (client_secret).") . '</strong>';
            $this->_html .= '<h4>' . $this->l($this->messageService) . '</h4>';
        }
        $this->_html .= '</fieldset><div class="clear"> </div>
            <style>
                #tabList { clear: left; }
                .tabItem { display: block; background: #FFFFF0; border: 1px solid #CCCCCC; padding: 10px; padding-top: 20px; }
            </style>
            <div id="tabList">
                <div class="tabItem">
                    <form action="index.php?tab=' . Tools::getValue('tab') . '&configure=' . Tools::getValue('configure') . '&token=' . Tools::getValue('token') . '&tab_module=' . Tools::getValue('tab_module') . '&module_name=' . Tools::getValue('module_name') . '&id_tab=1&section=general" method="post" class="form" id="configForm">
                    <fieldset style="border: 0px;">
                        <h4>' . $this->l('General configuration') . ' :</h4>

                        <label>' . $this->l("HealthyO client_id") . ' : </label>
                        <div class="margin-form"><input type="text" size="20" name="client_id" value="' . Tools::getValue('client_id', Configuration::get('client_id')) . '" /></div>
                        <label>' . $this->l("HealthyO client_secret") . ' : </label>
                        <div class="margin-form"><input type="text" size="20" name="client_secret" value="' . Tools::getValue('client_secret', Configuration::get('client_secret')) . '" /></div>
                    </div>
                    <br /><br />
                </fieldset>
                <div class="margin-form"><input class="button" name="submitSave" type="submit"></div>
            </form>
        </div></div>';
    }
    private function _postValidation()
    {
        // Check configuration values
        if (Tools::getValue('client_id') == '' || Tools::getValue('client_secret') == '') {
            $this->_postErrors[] = $this->l($this->messageService);
        }
    }
    private function _postProcess()
    {
        // Saving new configurations and Check authentication
        if (Configuration::updateValue('client_id', Tools::getValue('client_id'))
            && Configuration::updateValue('client_secret', Tools::getValue('client_secret'))
            && $this->login()){
            $this->_html .= $this->displayConfirmation($this->l('La configuration est terminée'));
        }
        else {
            $this->_html .= $this->displayError($this->l($this->authError));
        }
    }

    public function hookDisplayPersonalInformationTop($params)
    {
        // Make sure has been authorize before the action
        $this->login();
        $this->header[] = "Authorization: Bearer " . $this->authToken;

        // Then we integrate data send to ps_customer note
        $customerId = $params['cookie']->id_customer;
        $customer = new Customer($customerId);
        $this->log('$customerId :' . $customerId, 'info');
        $message = null;

        if ($customer) {
            if (Tools::isSubmit('healthyCheckForm'))
            {
                $has_insurance = Tools::getValue('has_insurance') == false ? "0" : "1";
                $has_prescription = Tools::getValue('has_insurance') == false ? "0" : "1";
                Configuration::updateValue('has_insurance', $has_insurance, false);
                Configuration::updateValue('has_prescription', $has_prescription, false);
                $message = "Success valide";
            }
            $this->log('$$has_insurance :' . $has_insurance, 'info');
            $this->log('$has_prescription :' . $has_prescription, 'info');
            // Update customer data in database
            if($has_insurance === 1){
                $customer->note = 'client dispose d\'une assurance santé';
            }
            if($has_prescription === 1){
                $customer->note .= "client dispose d'une ordonnance médicale";
            }
            $this->log('$customer->note :' . $customer->note, 'info');
            $customer->update();
        }

        // Prepare data for template
        $this->context->smarty->assign(array(
            'hasInsurance' => $has_insurance,
            'hasPrescription' => $has_prescription,
            'message' => $message,
            'select_healthy_option_url' => $this->context->link->getModuleLink(
                'healthyinfocheckout',
                'HealthyInfoCheckOut',
                ['id_customer' => $customerId]
            ),
        ));

        return $this->display(__FILE__, 'views/templates/front/checkout/_partials/personal-information.tpl');
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
