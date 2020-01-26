
<div id="info">
Configuraci&oacute;n inicial de  Drainware
</div>
<div class="assistant-wrapper first">

<div class="step" id="routes">
<div class="step-title">Configuraci&oacute;n de rutas</div>
<div class="step-body">
<div id="routes">
{foreach from=$routes item=route}
<div class="route">

<div class="controls">

<FORM action="?module=wizard&action=deleteStep2" method="post">   
<span class="route-label">Subred:</span>
<span class="route-value">{$route.network[0]}.{$route.network[1]}.{$route.network[2]}.{$route.network[3]}/{$route.mask}</span>

<span class="route-label">Puerta de enlace:</span>
<span class="route-value">{$route.gateway[0]}.{$route.gateway[1]}.{$route.gateway[2]}.{$route.gateway[3]}</span>


<INPUT type="hidden" class="ipgroup" name="group1a" maxlength="3" size="3" value="{$route.network[0]}">
<INPUT type="hidden" class="ipgroup" name="group2a" maxlength="3" size="3" value="{$route.network[1]}">
<INPUT type="hidden" class="ipgroup" name="group3a" maxlength="3" size="3" value="{$route.network[2]}">
<INPUT type="hidden" class="ipgroup" name="group4a" maxlength="3" size="3" value="{$route.network[3]}">
<INPUT type="hidden"  class="ipgroup" name="mask" maxlength="3" size="3" value="{$route.mask}">
<INPUT type="hidden" class="ipgroup" name="group1b" maxlength="3" size="3" value="{$route.gateway[0]}">
<INPUT type="hidden" class="ipgroup" name="group2b" maxlength="3" size="3" value="{$route.gateway[1]}">
<INPUT type="hidden" class="ipgroup" name="group3b" maxlength="3" size="3" value="{$route.gateway[2]}">
<INPUT type="hidden" class="ipgroup" name="group4b" maxlength="3" size="3" value="{$route.gateway[3]}">
<button onclick="$(this).submit()">Eliminar</button>
 </FORM>
</div>
</div>
{/foreach}
<br>
<div class="route">
<FORM id="newroute" action="?module=wizard&action=createStep2" method="post">   
Subred: 
<INPUT type="text" class="ipgroup" name="group1a" maxlength="3" size="3" value="{$ipa[0]}">.
<INPUT type="text" class="ipgroup" name="group2a" maxlength="3" size="3" value="{$ipa[1]}">.
<INPUT type="text" class="ipgroup" name="group3a" maxlength="3" size="3" value="{$ipa[2]}">.
<INPUT type="text" class="ipgroup" name="group4a" maxlength="3" size="3" value="{$ipa[3]}">/<INPUT type="text" class="ipgroup" name="mask" maxlength="3" size="3" value="{$ipa[3]}">
Gateway:
<INPUT type="text" class="ipgroup" name="group1b" maxlength="3" size="3" value="{$ipa[0]}">.
<INPUT type="text" class="ipgroup" name="group2b" maxlength="3" size="3" value="{$ipa[1]}">.
<INPUT type="text" class="ipgroup" name="group3b" maxlength="3" size="3" value="{$ipa[2]}">.
<INPUT type="text" class="ipgroup" name="group4b" maxlength="3" size="3" value="{$ipa[3]}"><br>
<br>

</FORM>
<button id="createroute">Agregar ruta</button>
</div>
</div>

<div class="step-form">
<button id="reboot">Terminar</button>
</div>
</div>
</div>





