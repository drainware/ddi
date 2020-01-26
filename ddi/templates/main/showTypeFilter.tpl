{ assign var="password" value="Password" }
{ assign var="repeat_please" value="Repeat, please" }
{ assign var="general_configuration" value="General Configuration" }
{ assign var="filter_type" value="Type of Filter" }
{ assign var="type_unique_policy" value="Unique Policy" }
{ assign var="type_policy_groups" value="Policy per Groups" }
{ assign var="change" value="Change" }

<div id="content_info">
  <h2><a href="#">{$software_version}</a></h2>
  <h3>{$translations[$general_configuration]}</h3>
</div>

<div id="filters" class="content_section">
  <h2>{$translations[$filter_type]}</h2>
  <div class="module_content">
    <form action="?module=main&action=modify" method="POST">
	  <input type="hidden" name="dditype" value="dditype" />
      <fieldset>
        <legend>{$translations[$filter_type]}</legend>
        <p>
          <input {if $sbmode eq "unique"}checked{/if} name="ddisbmode" value="unique" type="radio" />
          <label for="">{$translations[$type_unique_policy]}</label>
        </p>
        <p>
          <input {if $sbmode eq "groups"}checked{/if} name="ddisbmode" value="groups" type="radio"  />
          <label for="">{$translations[$type_policy_groups]}</label>
        </p>
      </fieldset>

      <input type="submit" class="button" value="{$translations[$change]}">

      <!--
      <table width="100%" cellpadding="0" cellspacing="20">
        <tr>
          <td><input {if $sbmode eq "unique"}checked{/if} name="ddisbmode" value="unique" type="radio"> Transparente</td>
          <td><input {if $sbmode eq "groups"}checked{/if} name="ddisbmode" value="groups" type="radio" onclick="if(!confirm('Atención: Esta opción requiere configuración en cada uno de los clientes.\nPor favor, seleccione esta opción solo si sabe lo que está haciendo.')){ldelim} return false; {rdelim};"> Por usuario</td>
        </tr>
        <tr>
          <td></td><td></td><td></td><td></td>
        </tr>
      </table>
      -->

    </form>
  </div>
</div>