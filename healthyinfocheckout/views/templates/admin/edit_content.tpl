{*
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to https://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2020 PrestaShop SA
*  @license    https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="panel">
  <form action="{$smarty.server.PHP_SELF|escape:'html':'UTF-8'}" method="post" class="form-horizontal">
    <div class="form-group">
      <label class="control-label col-lg-3">Content:</label>
      <div class="col-lg-9">
        <textarea name="content" class="form-control">{$content}</textarea>
      </div>
    </div>
    <div class="form-group">
      <div class="col-lg-offset-3 col-lg-9">
        <button type="submit" name="submitForm" class="btn btn-primary">Save</button>
      </div>
    </div>
  </form>
</div>

{if isset($confirmation) && $confirmation}
  <div class="alert alert-success">{$confirmation}</div>
{/if}





