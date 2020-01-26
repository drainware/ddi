{ assign var="thosts" value="Hosts" }
{ assign var="rangea" value="Range From" }
{ assign var="rangeb" value="Range To" }
{ assign var="create" value="Create" }
{ assign var="cancel" value="Cancel" }

<h2><a href="#">{$software_version}</a></h2>

<div id="wrapper-hosts">

<fieldset>
  <legend>{$translations[$thosts]}:</legend>


  <form id="newhost" action="?module=network&action=createHost" method="post">
	
	<fieldset>
    <legend>
        <div style="display:inline-block;margin-right:10px;margin-top:4px">
	Range <input type="radio" id="range_button" name="type" value="range" checked></div><div style="display:inline-block;">Ip <input type="radio" name="type" id="ip_button" value="ip">  
        </div>
	</legend>
	<div id="range">
    <p>
      <span class="entitle">{$translations[$rangea]}:</span>
      <input type="text" class="ipgroup text short required" max="255" id="ip_1" name="group1a" maxlength="3" size="3" value="{$ipa[0]}" />.
      <input type="text" class="ipgroup text short required" max="255" id="ip_2" name="group2a" maxlength="3" size="3" value="{$ipa[1]}" />.
      <input type="text" class="ipgroup text short required" max="255" id="ip_3" name="group3a" maxlength="3" size="3" value="{$ipa[2]}" />.
      <input type="text" class="ipgroup text short required" max="255" id="ip_4" name="group4a" maxlength="3" size="3" value="{$ipa[3]}" />
    </p>

    <p>
      <span class="entitle">{$translations[$rangeb]}:</span>
      <input type="text" class="ipgroup text short required" max="255" id="pe_1" name="group1b" maxlength="3" size="3" value="{$ipb[0]}" />.
      <input type="text" class="ipgroup text short required" max="255" id="pe_2" name="group2b" maxlength="3" size="3" value="{$ipb[1]}" />.
      <input type="text" class="ipgroup text short required" max="255" id="pe_3" name="group3b" maxlength="3" size="3" value="{$ipb[2]}" />.
      <input type="text" class="ipgroup text short required" max="255" id="pe_4" name="group4b" maxlength="3" size="3" value="{$ipb[3]}" />
    </p>
    </div>
    <div id="ip" style="display:none;">

    <p>
      <span class="entitle">Ip:</span>
      <input type="text" class="ipgroup text short" max="255" id="pe_1" name="group1" maxlength="3" size="3" value="{$ip[0]}" />.
      <input type="text" class="ipgroup text short" max="255" id="pe_2" name="group2" maxlength="3" size="3" value="{$ip[1]}" />.
      <input type="text" class="ipgroup text short" max="255" id="pe_3" name="group3" maxlength="3" size="3" value="{$ip[2]}" />.
      <input type="text" class="ipgroup text short" max="255" id="pe_4" name="group4" maxlength="3" size="3" value="{$ip[3]}" />
    </p>	
	
	</div>
    </fieldset>

    <div class="button_container">
      <input class="button mr_10" type="button" onclick="location.href='?module=network&action=showHost'" value="{$translations[$cancel]}" />
      <input class="button red" type="submit" id="createhost" value="{$translations[$create]}" />
    </div>

  </form>

</fieldset>

</div>
