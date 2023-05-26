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
  <div class="personal-information">
    <h3>Personal information</h3>
      <h5>{if $content} {$content} {else} {l s='Please fill in your personal information' mod='healthyinfocheckout'} {/if}</h5>
      <form
              class="clearfix"
              id="js-delivery"
              data-url-update="{url entity='module' name='healthyinfocheckout' controller='actions'
                  params=[
                  'process' => 'select'
              ]}"
              method="post"
      >
      <div class="form-group">
        <label class="form-control-label" for="input1">{l s='Extra information?' mod='healthyinfocheckout'}</label>
        <input type="text" name="extra_note"  class="form-control" id="extra-note" />
      </div>
      <div class="form-group">
        <label class="form-control-label" for="input2">{l s='Do you have health insurance?' mod='healthyinfocheckout'}</label>
        <input type="checkbox" name="has_insurance" value="has_insurance" {if $has_insurance}checked="checked"{/if} />
      </div>
      <div class="form-group">
        <label class="form-control-label" for="input3">{l s='Do you have prescription?' mod='healthyinfocheckout'}</label>
        <input type="checkbox" name="has_prescription" value="has_prescription" {if $has_prescription}checked="checked"{/if} />
      </div>
    </form>
  </div>
{/block}

