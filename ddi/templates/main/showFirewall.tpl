<div id="firewall" class="content_section hidden">

  <div class="module_content">
    <form  name="protocols" action="?module=main&action=modify" method="post"><br />

      <div class="overflow">
        <h2 class="select_all_label"><a name="protocols"></a>{$translations[$firewall]}</h2>
        <p class="select_all_input">
          <input type="checkbox" onclick="return changeSelDeSel3('protocheck');" name="all_protocols" />
          <label for="">{$translations[$all]}</label>
        </p>
      </div>

      <input type="hidden" name="ddiproto_thisisahack" value="" />

      {foreach name="myloop" from=$group_protocols key=groupname item=bloked_protocols}
        <fieldset class="accordion">
          <legend>{$translations[$groupname]}</legend>
          {foreach from=$bloked_protocols item=protocol }
            <p>
              <input type="checkbox" id="protocheck" name="{$protocol.property}" value="{if $protocol.value}on{else}off{/if}" {if $protocol.value}checked{/if} />
              <label for="">{$protocol.display}</label>
            </p>
          {/foreach}
        </fieldset>
      {/foreach}

      <input class="combos3 button" type="submit" value="{assign var="submit" value="Save"}{$translations[$submit]}" />

      <!--
      <table>
        <tr>
          {foreach name="myloop" from=$group_protocols key=groupname item=bloked_protocols}
            {if $smarty.foreach.myloop.index is div by 3}</tr><tr>{/if}
            <td valign="top">
              <h2>{$translations[$groupname]}</h2>
              {foreach from=$bloked_protocols item=protocol }
                <input type="checkbox" id="protocheck" name="{$protocol.property}" value="{if $protocol.value}on{else}off{/if}" {if $protocol.value}checked{/if}> {$protocol.display}<br />
              {/foreach}
            </td>
          {/foreach}
        </tr>
      </table>
      <br />
      <input class="combos3 button" type="submit" value="{assign var="submit" value="Save"}{$translations[$submit]}" />
      -->

    </form>
  </div>
</div>

<!--
<div id="info">Puertos bloqueados</div>
<div class="module_content">
<form action ="?module=main&action=modify" method="POST">
<table width="100%" cellpadding="0" cellspacing="5">
<tr>
<td>Puertos separados por comas:<br /><textarea cols=100 rows=5 name="ports">{$ports_list}</textarea></td>
</tr>
</table>
<input type="submit" value="{$translations[$change]}">
</form>
</div>
<br />
-->