{extends '@PrestaShop/Admin/layout.html.twig'}
{block 'content'}
<div class="panel">
  <form action="{$smarty.server.PHP_SELF|escape:'html':'UTF-8'}" method="post" class="form-horizontal">
    <div class="form-group">
      <label class="control-label col-lg-3">Content:</label>
      <div class="col-lg-9">
        <textarea name="content" class="form-control">
          {if isset($form.content) && !empty($form.content)}
             {$form.content|escape:'html':'UTF-8'}
          {/if}
        </textarea>
      </div>
    </div>
    <div class="form-group">
      <div class="col-lg-offset-3 col-lg-9">
        <button type="submit" name="submitForm" class="btn btn-primary">Save</button>
      </div>
    </div>
  </form>
</div>
{/block}





