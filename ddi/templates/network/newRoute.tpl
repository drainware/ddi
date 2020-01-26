{ assign var="troutes" value="Routes" }
{ assign var="sub_network" value="Subnet" }
{ assign var="gateway" value="Gateway" }
{ assign var="create" value="Create" }
{ assign var="cancel" value="Cancel" }

<h2><a href="#">{$software_version}</a></h2>

<div id="wrapper-routes">

<fieldset>
  <legend>{$translations[$troutes]}:</legend>


  <form id="newroute" action="?module=network&action=createRoute" method="post">
    <p>
      <span class="entitle">{$translations[$sub_network]}:</span>
      <input type="text" class="ipgroup text short required" max="255" id="ip_1" name="group1a" maxlength="3" size="3" value="{$ipa[0]}" />.
      <input type="text" class="ipgroup text short required" max="255" id="ip_2" name="group2a" maxlength="3" size="3" value="{$ipa[1]}" />.
      <input type="text" class="ipgroup text short required" max="255" id="ip_3" name="group3a" maxlength="3" size="3" value="{$ipa[2]}" />.
      <input type="text" class="ipgroup text short required" max="255" id="ip_4" name="group4a" maxlength="3" size="3" value="{$ipa[3]}" />/
      <input type="text" class="ipgroup text short required" max="255" id="ip_5" name="mask" maxlength="3" size="3" value="{$ipa[3]}" />
    </p>

    <p>
      <span class="entitle">{$translations[$gateway]}:</span>
      <input type="text" class="ipgroup text short required" max="255" id="pe_1" name="group1b" maxlength="3" size="3" value="{$ipa[0]}" />.
      <input type="text" class="ipgroup text short required" max="255" id="pe_2" name="group2b" maxlength="3" size="3" value="{$ipa[1]}" />.
      <input type="text" class="ipgroup text short required" max="255" id="pe_3" name="group3b" maxlength="3" size="3" value="{$ipa[2]}" />.
      <input type="text" class="ipgroup text short required" max="255" id="pe_4" name="group4b" maxlength="3" size="3" value="{$ipa[3]}" />
      <input type="hidden" name="action" value="block" />
    </p>

    <div class="button_container">
      <input class="button mr_10" type="button" onclick="location.href='?module=route'" value="{$translations[$cancel]}" />
      <input class="button red" type="submit" id="createroute" value="{$translations[$create]}" />
    </div>

  </form>

</fieldset>

</div>
