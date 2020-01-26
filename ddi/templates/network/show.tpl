{ assign var="configuration_wizard" value="Configuration Wizard" }
{ assign var="ip_configuration" value="Ip Configuration" }
{ assign var="step" value="Step" }
{ assign var="text_step1" value="Text Step 1" }
{ assign var="static_ip" value="Static Ip" }
{ assign var="text_step2" value="Text Step 2" }
{ assign var="mask" value="Mask" }
{ assign var="gateway" value="Gateway" }
{ assign var="dns_configuration" value="DNS Configuration" }
{ assign var="text_step3" value="Text Step 3" }
{ assign var="first_server" value="First Server" }
{ assign var="second_server" value="Second Server" }
{ assign var="finalize" value="Finalize" }
{ assign var="text_step4" value="Text Step 4" }
{ assign var="step" value="Step" }
{ assign var="next_step" value="Next" }
{ assign var="previous_step" value="Previous" }
{ assign var="cancel" value="Cancel" }
{ assign var="save" value="Save" }

<SCRIPT LANGUAGE="JavaScript">

var wizard_step = "{$translations[$step]}";
var wizard_next = "{$translations[$next_step]}";
var wizard_previous = "{$translations[$previous_step]}";
var wizard_cancel = "{$translations[$cancel]}";

</SCRIPT>

<h2>{$translations[$configuration_wizard]}</h2>

<div class="assistant-wrapper">
  <form id="form-assistant" class="wizard_form" method="post" action="?module=network&action=save">

    <fieldset class="dwstep">
      <legend>{$translations[$ip_configuration]}</legend>
      <p class="intro">
        {$translations[$text_step1]}
      </p>
      <p>
        <!--<span class="entitle">DHCP</span>
        <input type="radio" id="dhcp" name="step1" value="dhcp" checked="checked" />-->
        <input type="hidden" id="dhcp" name="step1" value="dhcp"  />
      </p>
      <p>
        <!--<span class="entitle">{$translations[$static_ip]}</span>
        <input type="radio" id="static" name="step1" value="static" >-->
        <input type="hidden" id="static" name="step1" value="static" checked="checked" />
      </p>
    </fieldset>

    <fieldset class="dwstep">
      <legend>{$translations[$ip_configuration]} (II)</legend>
      <p class="intro">
        {$translations[$text_step2]}
      </p>
      <p>
        <span class="entitle_medium">{$translations[$static_ip]}:</span>
        <input type="text" class="ipgroup text short required" max="255" name="group1a" maxlength="3" size="3" value="{$ipa[0]}" />.
        <input type="text" class="ipgroup text short required" max="255" name="group2a" maxlength="3" size="3" value="{$ipa[1]}" />.
        <input type="text" class="ipgroup text short required" max="255" name="group3a" maxlength="3" size="3" value="{$ipa[2]}" />.
        <input type="text" class="ipgroup text short required" max="255" name="group4a" maxlength="3" size="3" value="{$ipa[3]}" />
      </p>
      <p>
        <span class="entitle_medium">{$translations[$mask]}:</span>
        <input type="text" class="ipgroup text short required" max="255" name="group1b" maxlength="3" size="3" value="{$ipa[0]}" />.
        <input type="text" class="ipgroup text short required" max="255" name="group2b" maxlength="3" size="3" value="{$ipa[1]}" />.
        <input type="text" class="ipgroup text short required" max="255" name="group3b" maxlength="3" size="3" value="{$ipa[2]}" />.
        <input type="text" class="ipgroup text short required" max="255" name="group4b" maxlength="3" size="3" value="{$ipa[3]}" />
      </p>
      <p>
        <span class="entitle_medium">{$translations[$gateway]}:</span>
        <input type="text" class="ipgroup text short required" max="255" name="group1c" maxlength="3" size="3" value="{$ipa[0]}" />.
        <input type="text" class="ipgroup text short required" max="255" name="group2c" maxlength="3" size="3" value="{$ipa[1]}" />.
        <input type="text" class="ipgroup text short required" max="255" name="group3c" maxlength="3" size="3" value="{$ipa[2]}" />.
        <input type="text" class="ipgroup text short required" max="255" name="group4c" maxlength="3" size="3" value="{$ipa[3]}" />
      </p>
    </fieldset>

    <fieldset class="dwstep">
      <legend>{$translations[$dns_configuration]}</legend>
      <p class="intro">
        {$translations[$text_step3]}
      </p>
      <p>
        <span class="entitle_medium">{$translations[$first_server]}:</span>
        <input type="text" class="ipgroup text short required" max="999" name="dnsgroup1a" maxlength="3" size="3" value="{$ipa[0]}" />.
        <input type="text" class="ipgroup text short required" max="999" name="dnsgroup2a" maxlength="3" size="3" value="{$ipa[1]}" />.
        <input type="text" class="ipgroup text short required" max="999" name="dnsgroup3a" maxlength="3" size="3" value="{$ipa[2]}" />.
        <input type="text" class="ipgroup text short required" max="999" name="dnsgroup4a" maxlength="3" size="3" value="{$ipa[3]}" />
      </p>
      <p>
        <span class="entitle_medium">{$translations[$second_server]}:</span>
        <input type="text" class="ipgroup text short required" max="999" name="dnsgroup1b" maxlength="3" size="3" value="{$ipa[0]}" />.
        <input type="text" class="ipgroup text short required" max="999" name="dnsgroup2b" maxlength="3" size="3" value="{$ipa[1]}" />.
        <input type="text" class="ipgroup text short required" max="999" name="dnsgroup3b" maxlength="3" size="3" value="{$ipa[2]}" />.
        <input type="text" class="ipgroup text short required" max="999" name="dnsgroup4b" maxlength="3" size="3" value="{$ipa[3]}" />
      </p>
    </fieldset>

    <fieldset class="dwstep">
      <legend>{$translations[$finalize]}</legend>
      <p class="intro">
        {$translations[$text_step4]}
      </p>
      <input class="next button" type="submit" id="saveForm" value="{$translations[$save]}" />
    </fieldset>

  </form>

</div>
