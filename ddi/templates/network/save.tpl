{ assign var="configuration_wizard" value="Configuration Wizard" }
{ assign var="text_save1" value="Text Save 1" }
{ assign var="text_save2" value="Text Save 2" }
{ assign var="continue" value="Continue" }
{ assign var="reload" value="Reload" }
{ assign var="reload_message" value="Reload Message" }
{ assign var="change" value="Change" }
{ assign var="password" value="Password" }
{ assign var="repeat_please" value="Repeat, please" }



<fieldset>
  <legend>{$translations[$configuration_wizard]}</legend>
  <div id="content_info">
    <h2>{$translations[$text_save1]}</h2>
    <h3 class="success">{$translations[$text_save2]}</h3>
  </div>

  <input class="button" type="button" id="continuar" onclick="if (confirm('{$translations[$reload_message]}')) location.href='?module=network&action=reload'; else location.href='';" href="http://192.168.23.40/ddi/" value="{$translations[$reload]}" />

</fieldset>
