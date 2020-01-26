{ assign var="block_extensions" value="Block Extensions" }
{ assign var="submit" value="Save" }

<h2><a name="extensions"></a>{$translations[$block_extensions]}</h2>
<div class="module_content">
  <form  name="extensions" action="?module=group&action=modify" method="post">

    <!-- @TODO: It causes an error when saving, we remove it until version 2.0
    <input type="checkbox" onclick="return changeSelDeSel2('extcheck');" name="all_extensions"  />Todas<br />
    -->
    <input type="hidden" name="gid" value="{$gid}" />
    <input type="hidden" name="ddiext_thisisahack" value="" />

    <fieldset>
        <legend>{$translations[$block_extensions]}</legend>
	  <input type="hidden" name="name" value="{$group_name}">
      {foreach from=$group_extensions item=extension }
        <p>
          <input type="checkbox" id="extcheck" name="blocked_extensions[]" value="{$extension.display}" {if $extension.value}checked{/if} />
          <label for="">{$extension.display}</label>
        </p>
      {/foreach}
    </fieldset>

    <input class="combos3 button" type="submit" value="{$translations[$submit]}" />

  </form>
</div>
