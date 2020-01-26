{ assign var="route" value="route" }
{ assign var="room" value="room" }
{ assign var="network" value="network" }
{ assign var="hostwl" value="White List" }



          <ul>
            {if $smarty.get.action eq "show" || $smarty.get.action eq ''}
              <li><a id="opt_network_wizard" href="#" class="active"><span>{$translations[$network]}</span></a></li>
              <li><a id="opt_network_routes" href="?module=network&action=showRoute"><span>{$translations[$route]}</span></a></li>
              <!--
              <li><a id="opt_network_rooms" href="?module=network&action=showRoom"><span>{$translations[$room]}</span></a></li>
              <li><a id="opt_network_hostwl" href="?module=network&action=showHost"><span>{$translations[$hostwl]}</span></a></li>
              <li><a href="?module=network&action=firewall" id="opt_firewall"><span>Cortafuegos</span></a></li>
              -->
              
            {elseif $smarty.get.action|strstr:"Room"}
              <li><a id="opt_network_wizard" href="?module=network&action=show"><span>{$translations[$network]}</span></a></li>
              <li><a id="opt_network_routes" href="?module=network&action=showRoute"><span>{$translations[$route]}</span></a></li>
              <!--
              <li><a id="opt_network_rooms" href="?module=network&action=showRoom" class="active"><span>{$translations[$room]}</span></a></li>
              <li><a id="opt_network_hostwl" href="?module=network&action=showHost"><span>{$translations[$hostwl]}</span></a></li>
              <li><a href="?module=network&action=firewall" id="opt_firewall"><span>Cortafuegos</span></a></li>
              -->
              
            {elseif $smarty.get.action|strstr:"Route"}

              <li><a id="opt_network_wizard" href="?module=network&action=show"><span>{$translations[$network]}</span></a></li>
              <li><a id="opt_network_routes" href="?module=network&action=showRoute" class="active"><span>{$translations[$route]}</span></a></li>
              <!--
              <li><a id="opt_network_rooms" href="?module=network&action=showRoom"><span>{$translations[$room]}</span></a></li>
              <li><a id="opt_network_hostwl" href="?module=network&action=showHost"><span>{$translations[$hostwl]}</span></a></li>
              <li><a href="?module=network&action=firewall" id="opt_firewall"><span>Cortafuegos</span></a></li>
              -->
              
            {elseif $smarty.get.action|strstr:"Host"}
              <li><a id="opt_network_wizard" href="?module=network&action=show"><span>{$translations[$network]}</span></a></li>
              <li><a id="opt_network_routes" href="?module=network&action=showRoute"><span>{$translations[$route]}</span></a></li>
              <!--
              <li><a id="opt_network_rooms" href="?module=network&action=showRoom"><span>{$translations[$room]}</span></a></li>
              <li><a id="opt_network_hostwl" href="?module=network&action=showHost" class="active"><span>{$translations[$hostwl]}</span></a></li>
              <li><a href="?module=network&action=firewall" id="opt_firewall"><span>Cortafuegos</span></a></li>
              -->
              
            {elseif $smarty.get.action|strstr:"firewall"}
              <li><a id="opt_network_wizard" href="?module=network&action=show"><span>{$translations[$network]}</span></a></li>
              <li><a id="opt_network_routes" href="?module=network&action=showRoute"><span>{$translations[$route]}</span></a></li>
              <!--
              <li><a id="opt_network_rooms" href="?module=network&action=showRoom"><span>{$translations[$room]}</span></a></li>
              <li><a id="opt_network_hostwl" href="?module=network&action=showHost"><span>{$translations[$hostwl]}</span></a></li>
              <li><a class="active" href="#" id="opt_firewall"><span>Cortafuegos</span></a></li>
              -->
            {/if}
              
          </ul>
