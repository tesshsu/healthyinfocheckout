<?php

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace PrestaShop\Module\HealthyInfoChekcout\Controller;

use AdminController;
use Doctrine\Common\Cache\CacheProvider;
use HealthyInfoCheckOut;
use HelperForm;
use Tools;

class AdminHealthyInfoController extends FrameworkBundleAdminController
{
    public $name = 'healthyinfocheckout';
    /** @var healthyinfocheckout */
    public $module;

    /**
     * @var CacheProvider
     */
    private $cache;

    /**
     * @var int|null
     */
    private $shopId;
    private $context;
    private $controller_name;

    public function __construct(CacheProvider $cache, $shopId)
    {
        $this->cache = $cache;
        $this->shopId = $shopId;
    }

    public function renderForm()
    {
        // Get the content
        $content = tools::getValue('content');

        // Create the form fields
        $fieldsForm = array(
            'form' => array(
                'legend' => array(
                    'title' => 'Healthy Info Checkout',
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'textarea',
                        'label' => 'Content',
                        'name' => 'description',
                        'rows' => 10,
                        'cols' => 80,
                        'required' => true,
                        'value' => $content,
                    ),
                ),
                'submit' => array(
                    'title' => 'Save',
                ),
            ),
        );

        // Create the HelperForm instance
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->controller_name;
        $helper->token = Tools::getAdminTokenLite('AdminHealthyInfo');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->show_toolbar = false;
        $helper->submit_action = 'saveForm';
        $helper->fields_value = array(
            'content' => $content,
        );

        // Generate the form
        return $helper->generateForm(array($fieldsForm));
    }

    public function renderView()
    {
        $this->context->smarty->assign(array(
            'form' => $this->renderForm(),
        ));

        $this->getTemplatePath('edit_content.tpl');
        //$this->display(__FILE__, 'views/templates/admin/edit_content.tpl');
    }

    public function initContent()
    {
        parent::initContent();
        $this->content .= $this->renderView();
    }

    public function getTemplatePath()
    {
        return $this->module->getLocalPath() . 'views/templates/admin/';
    }
}
