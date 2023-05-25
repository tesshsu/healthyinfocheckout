<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

/**
 * @since 1.5.0
 */
class HealthyInfoCheckOutActionsModuleFrontController extends ModuleFrontController
{

    public function init()
    {
        parent::init();
    }

    public function display()
    {
        return '';
    }

    public function postProcess()
    {
        if (Tools::getValue('process') == 'select') {
                     $this->processSelect();
        }
        return parent::postProcess(); // IF YOU DON'T DO THIS FOR ANY REASON
    }

    /**
     * Update customer data
     */
    public function processSelect()
    {
        Logger::log('processSelect', 'info');
        $context = Context::getContext();
        $customerId = $context->customer->id;
        $customer = new Customer($customerId);

        $has_insurance = Tools::getValue('has_insurance') == false ? "0" : "1";
        $has_prescription = Tools::getValue('has_prescription') == false ? "0" : "1";
        $text_has_insurance = 'client dispose d\'une assurance santé';
        $text_has_prescription = 'client dispose d\'une ordonnance médicale';
        $extra_note = Tools::getValue('extra_note');

        // Update customer  and order data in database
        if($context->customer->isLogged() && $has_insurance == 1 || $has_prescription == 1) {
            // Update customer note
           if ($has_insurance == 1 && $has_prescription == 1) {
               $customer->note = $text_has_insurance . ' et aussi ' . $text_has_prescription;
           } elseif ($has_insurance == 1) {
               $customer->note = $text_has_insurance;
           } elseif ($has_prescription == 1) {
               $customer->note = $text_has_prescription;
           }
            // Keep log if still in development
            Logger::log('$customer->note controller TEST :' . $customer->note, 'info');
            $customer->update();
            // Insert into ps_healthy_info_checkout table
            $db = Db::getInstance();
            $data = array(
                'id_customer' => (int)$customerId,
                'has_insurance' => (int)$has_insurance,
                'has_prescription' => (int)$has_prescription,
                'extra_note' => pSQL($extra_note),
                'created_at' => date('Y-m-d H:i:s')
            );
            $db->insert('healthy_info_checkout', $data);
            Logger::log('insert healthy_info_checkout : '. $data, 'info');
        }
    }
}
