{ assign var="block_categories" value="Block Categories" }
{ assign var="submit" value="Save" }


<div id="categories">
  <h2><a name="categories"></a>{$translations[$block_categories]}</h2>
  <div class="module_content">
    <form  name="categories" action="?module=webfilter&action=modify" method="post">
      <!-- @TODO: It causes an error when saving, we remove it until version 2.0
      <input type="checkbox" onclick="return changeSelDeSel('catcheck');" name="all_categories"  />Todas<br />
      -->

      <input type="hidden" name="gid" value="{$gid}" />
      <input type="hidden" name="ddicat_thisisahack" value="" />
      <input type="hidden" name="name" value="{$group_name}">
      {foreach name="myloop" from=$group_categories key=groupname item=bloked_categories}
        <fieldset class="accordion">
          <legend>{$translations[$groupname]}</legend>
          {foreach from=$bloked_categories item=category }
            {if $category.display != "adv"}
                <p>
                <input type="checkbox" id="catcheck" name="blocked_categories[]" {if $category.value}checked{/if} value="{$category.display}" {if $category.value}checked{/if} />
                <label for="">{$translations[$category.display]}</label>
                </p>
            {/if}
          {/foreach}
        </fieldset>
      {/foreach}

      <input class="combos3 button" type="submit" value="{$translations[$submit]}" />

    </form>
  </div>
</div>
