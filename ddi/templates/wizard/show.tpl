{ assign var="change" value="Change" }
{ assign var="password" value="Password" }
{ assign var="repeat_please" value="Repeat, please" }

<h2>Configuraci&oacute;n inicial de  Drainware</h2>

<form id="wizard-form" class="wizard_form" method="post" action="?module=wizard&action=saveStep1">
  <fieldset>
    <legend>Bienvenido a Drainware</legend>
    <p class="intro">
      Usted ha iniciado el dispositivo Drainware por primera vez.
      Una vez completado este proceso podr&aacute; acceder a la configuraci&oacute;n normal y nunca volver&aacute a ver este mensaje.
      <input type="hidden" name="dummy" value="dummy" id="dummy" />
    </p>
  </fieldset>

  <fieldset>
    <legend>Configuraci&oacute;n IP</legend>
    <p>
      <span class="entitle_medium">DHCP:</span>
      <input type="radio" class="validate[required] radio" id="dhcp" name="step1" value="dhcp" value="checked" checked="checked" />
    </p>
    <p>
      <span class="entitle_medium">IP Est&aacute;tica:</span>
      <input type="radio" class="validate[required] radio" id="static" name="step1" value="static" />
    </p>
  </fieldset>

  <fieldset>
    <legend>Configuraci&oacute;n IP (II)</legend>
    <p class="intro">
      Este asistente le guiar&aacute; para la configuraci&oacute;n de red de su dispositivo Drainware.
      Eliga su IP est&aacute;tica y defina la m&aacute;scara de subred a utilizar.
    </p>
    <p>
      <span class="entitle_medium">IP est&aacute;tica:</span>
      <input type="text" class="ipgroup text short required" max="255" id="ip_1" name="group1a" maxlength="3" size="3" value="{$ipa[0]}">.
      <input type="text" class="ipgroup text short required" max="255" id="ip_2" name="group2a" maxlength="3" size="3" value="{$ipa[1]}">.
      <input type="text" class="ipgroup text short required" max="255" id="ip_3" name="group3a" maxlength="3" size="3" value="{$ipa[2]}">.
      <input type="text" class="ipgroup text short required" max="255" id="ip_4" name="group4a" maxlength="3" size="3" value="{$ipa[3]}">
    </p>
    <p>
      <span class="entitle_medium">M&aacute;scara de subred:</span>
      <input type="text" class="ipgroup text short required" max="255" id="mr_1" name="group1b" maxlength="3" size="3" value="{$ipa[0]}">.
      <input type="text" class="ipgroup text short required" max="255" id="mr_2" name="group2b" maxlength="3" size="3" value="{$ipa[1]}">.
      <input type="text" class="ipgroup text short required" max="255" id="mr_3" name="group3b" maxlength="3" size="3" value="{$ipa[2]}">.
      <input type="text" class="ipgroup text short required" max="255" id="mr_4" name="group4b" maxlength="3" size="3" value="{$ipa[3]}">
    </p>
    <p>
      <span class="entitle_medium">Puerta de enlace:</span>
      <input type="text" class="ipgroup text short required" max="255" id="pe_1" name="group1c" maxlength="3" size="3" value="{$ipa[0]}">.
      <input type="text" class="ipgroup text short required" max="255" id="pe_2" name="group2c" maxlength="3" size="3" value="{$ipa[1]}">.
      <input type="text" class="ipgroup text short required" max="255" id="pe_3" name="group3c" maxlength="3" size="3" value="{$ipa[2]}">.
      <input type="text" class="ipgroup text short required" max="255" id="pe_4" name="group4c" maxlength="3" size="3" value="{$ipa[3]}">
    </p>
  </fieldset>

  <fieldset>
    <legend>Configuraci&oacute;n DNS</legend>
    <p class="intro">
      Este asistente le guiar&aacute; para la configuraci&oacute;n de red de su dispositivo Drainware.
      Eliga su IP est&aacute;tica y defina la m&aacute;scara de subred a utilizar.
    </p>
    <p>
      <span class="entitle_medium">Servidor primario:</span>
      <input type="text" class="ipgroup text short required digits" id="sp_1" name="dnsgroup1a" maxlength="3" size="3" value="{$ipa[0]}">.
      <input type="text" class="ipgroup text short required digits" id="sp_2" name="dnsgroup2a" maxlength="3" size="3" value="{$ipa[1]}">.
      <input type="text" class="ipgroup text short required digits" id="sp_3" name="dnsgroup3a" maxlength="3" size="3" value="{$ipa[2]}">.
      <input type="text" class="ipgroup text short required digits" id="sp_4" name="dnsgroup4a" maxlength="3" size="3" value="{$ipa[3]}">
    </p>
    <p>
      <span class="entitle_medium">Servidor secundario:</span>
      <input type="text" class="ipgroup text short required digits" id="ss_1" name="dnsgroup1b" maxlength="3" size="3" value="{$ipa[0]}">.
      <input type="text" class="ipgroup text short required digits" id="ss_2" name="dnsgroup2b" maxlength="3" size="3" value="{$ipa[1]}">.
      <input type="text" class="ipgroup text short required digits" id="ss_3" name="dnsgroup3b" maxlength="3" size="3" value="{$ipa[2]}">.
      <input type="text" class="ipgroup text short required digits" id="ss_4" name="dnsgroup4b" maxlength="3" size="3" value="{$ipa[3]}">
    </p>
    <input class="next button" type="submit" id="saveForm" value="Finalizar" />
  </fieldset>

</form>

