{ assign var="firewall" value="Firewall" }
{ assign var="submit" value="Save" }

<h2><a href="#">{$software_version}</a></h2>
<div id="firewall">
  <h2>{$translations[$firewall]}</h2>
  <div class="module_content">
    <form  name="firewall" action="?module=network&action=firewallModify" method="post">
      <!-- @TODO: It causes an error when saving, we remove it until version 2.0
      <input type="checkbox" onclick="return changeSelDeSel('catcheck');" name="all_firewall"  />Todas<br />
      -->
      <input type="hidden" name="gid" value="{$gid}" />
      <input type="hidden" name="ddicat_thisisahack" value="" />
      <input type="hidden" name="name" value="{$group_name}">
      {foreach name="myloop" from=$firewall_categories key=groupname item=protocols}
        <fieldset class="accordion">
          <legend>{$translations[$groupname]}</legend>
          {foreach from=$protocols item=protocol }
            <p>
              <input type="checkbox" id="protcheck" name="blocked_protocols[]" {if $protocol.activated}checked{/if} value="{$protocol.name}" {if $protocol.activated}checked{/if} />
              <label for="">{$translations[$protocol.name]}</label>
            </p>
          {/foreach}
        </fieldset>
      {/foreach}

      <input class="combos3 button" type="submit" value="{$translations[$submit]}" />

    </form>
  </div>
</div>
