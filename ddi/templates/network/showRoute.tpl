{ assign var="troutes" value="Routes" }
{ assign var="sub_network" value="Subnet" }
{ assign var="gateway" value="Gateway" }
{ assign var="new_route" value="New Route" }
{ assign var="remove" value="Remove" }
{ assign var="change" value="Change" }
{ assign var="password" value="Password" }
{ assign var="repeat_please" value="Repeat, please" }

<h2><a href="#">{$software_version}</a></h2>

<div id="wrapper-routes">

  <fieldset>
    <legend>{$translations[$troutes]}</legend>

    <div id="routes">

      {foreach from=$routes item=route}

        <div class="route">

          <div class="controls">

            <form action="?module=network&action=deleteRoute" method="post">
              <span class="route-label">{$translations[$sub_network]}:</span>
              <span class="route-value">{$route.network[0]}.{$route.network[1]}.{$route.network[2]}.{$route.network[3]}/{$route.mask}</span>

              <span class="route-label">{$translations[$gateway]}:</span>
              <span class="route-value">{$route.gateway[0]}.{$route.gateway[1]}.{$route.gateway[2]}.{$route.gateway[3]}</span>

              <input type="hidden" class="ipgroup" name="group1a" maxlength="3" size="3" value="{$route.network[0]}" />
              <input type="hidden" class="ipgroup" name="group2a" maxlength="3" size="3" value="{$route.network[1]}" />
              <input type="hidden" class="ipgroup" name="group3a" maxlength="3" size="3" value="{$route.network[2]}" />
              <input type="hidden" class="ipgroup" name="group4a" maxlength="3" size="3" value="{$route.network[3]}" />
              <input type="hidden" class="ipgroup" name="mask" maxlength="3" size="3" value="{$route.mask}" />
              <input type="hidden" class="ipgroup" name="group1b" maxlength="3" size="3" value="{$route.gateway[0]}" />
              <input type="hidden" class="ipgroup" name="group2b" maxlength="3" size="3" value="{$route.gateway[1]}" />
              <input type="hidden" class="ipgroup" name="group3b" maxlength="3" size="3" value="{$route.gateway[2]}" />
              <input type="hidden" class="ipgroup" name="group4b" maxlength="3" size="3" value="{$route.gateway[3]}" />
              <button onclick="$(this).submit()">{$translations[$remove]}</button>
            </form>

          </div>

        </div>

      {/foreach}

    </div>

    <div class="button_container">
      <a class="button" href="?module=network&action=newRoute">{$translations[$new_route]}</a>
    </div>

  </fieldset>

</div>
