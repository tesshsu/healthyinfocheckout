{*
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
*}
{block name='healthyCheckForm'}
  {if $message}
    <div class="alert alert-success">{l s='Your personal information has been updated.' d='Shop.Notifications.Success'}</div>
  {/if}
  <div class="personal-information">
    <h3>Personal information</h3>
    <form action="" method="post" class="form" name="healthyCheckForm">
      <div class="form-group">
        <label class="form-control-label" for="input1">{l s='Do you have health insurance?' mod='healthyinfocheckout'}</label>
        <input type="checkbox" name="has_insurance" value="has_insurance" {if $hasInsurance}checked="checked"{/if} </>
      </div>
      <div class="form-group">
        <label class="form-control-label" for="input1">{l s='Do you have prescription?' mod='healthyinfocheckout'}</label>
        <input type="checkbox" name="has_prescription" value="has_prescription" {if $hasPrescription}checked="checked"{/if} </>
      </div>
      <div class="form-group">
        <button type="submit" name="healthyCheckForm" class="btn btn-primary">{l s='Save' mod='healthyinfocheckout'}</button>
      </div>
    </form>
  </div>
{/block}

