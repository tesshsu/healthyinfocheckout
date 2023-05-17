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
class healthyInfoContentModel extends ObjectModel
{
    /**
     * Identifier of HealthyInfoCheckout
     *
     * @var int
     */
    public $id;
    /**
     * HTML format of content
     *
     * @var array
     */
    public $content;
    /**
     * Date of creation
     *
     * @var string
     */
    public $created_at;
    /**
     * Identifier of admin update
     *
     * @var string
     */
    public $updated_by;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'healthy_info_content',
        'primary' => 'id_healthy_info',
        'fields' => [
            'content' => ['type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'required' => true],
            'created_at' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
            'updated_by' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
        ],
    ];
}
