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
class healthyInfoCheckoutModel extends ObjectModel
{
    /**
     * Identifier of HealthyInfoCheckout
     *
     * @var int
     */
    public $id;

    /**
     * Identifier of customer
     *
     * @var int
     */
    public $id_customer;

    /**
     * boolean of has insurance
     *
     * @var boolean
     */
    public $has_insurance;

    /**
     * boolean of has prescription
     *
     * @var boolean
     */
    public $has_prescription;

    /**
     * HTML format of Extra note
     *
     * @var array
     */
    public $extra_note;

    /**
     * HTML format of content
     *
     * @var array
     */
    public $content;

    /** @var string Object creation date */
    public $created_at;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'healthy_info_checkout',
        'primary' => 'id_healthy_info',
        'fields' => [
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'has_insurance' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'has_prescription' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'extra_note' => ['type' => self::TYPE_STRING, 'validate' => 'isMessage', 'size' => 300],
            'content' => ['type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'],
            'created_at' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];
}
