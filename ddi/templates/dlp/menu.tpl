{ assign var="t_dlp_policies" value="Policies" }
{ assign var="t_dlp_rules" value="Rules" }
{ assign var="t_dlp_files" value="Files" }
{ assign var="t_dlp_network_places" value="Network Places" }
{ assign var="t_dlp_advanced" value="Advanced" }
{ assign var="t_dlp_menu_applications" value="Applications" }


          <ul>
            {if $smarty.get.action eq "show" || $smarty.get.action eq ''}
              <li><a id="opt_dlp_policies" href="#" class="active"><span>{$translations[$t_dlp_policies]}</span></a></li>
              <li><a id="opt_dlp_rules" href="?module=dlp&action=showRules"><span>{$translations[$t_dlp_rules]}</span></a></li>
              <li><a id="opt_dlp_files" href="?module=dlp&action=showFiles"><span>{$translations[$t_dlp_files]}</span></a></li>
              <li><a id="opt_dlp_network_places" href="?module=dlp&action=showNetworkPlaces"><span>{$translations[$t_dlp_network_places]}</span></a></li>
              <li><a id="opt_dlp_applications" href="?module=dlp&action=showApplications"><span>{$translations[$t_dlp_menu_applications]}</span></a></li>
              <li><a id="opt_dlp_confg" href="?module=dlp&action=showAdvanced"><span>{$translations[$t_dlp_advanced]}</span></a></li>
            {elseif $smarty.get.action eq "showRules" || $smarty.get.action eq ''}
              <li><a id="opt_dlp_policies" href="?module=dlp&action=show"><span>{$translations[$t_dlp_policies]}</span></a></li>
              <li><a id="opt_dlp_rules" href="#" class="active"><span>{$translations[$t_dlp_rules]}</span></a></li>
              <li><a id="opt_dlp_files" href="?module=dlp&action=showFiles"><span>{$translations[$t_dlp_files]}</span></a></li>
              <li><a id="opt_dlp_network_places" href="?module=dlp&action=showNetworkPlaces"><span>{$translations[$t_dlp_network_places]}</span></a></li>
              <li><a id="opt_dlp_applications" href="?module=dlp&action=showApplications"><span>{$translations[$t_dlp_menu_applications]}</span></a></li>
              <li><a id="opt_dlp_confg" href="?module=dlp&action=showAdvanced"><span>{$translations[$t_dlp_advanced]}</span></a></li>  
            {elseif $smarty.get.action eq "showFiles"}
              <li><a id="opt_dlp_policies" href="?module=dlp&action=show"><span>{$translations[$t_dlp_policies]}</span></a></li>
              <li><a id="opt_dlp_rules" href="?module=dlp&action=showRules"><span>{$translations[$t_dlp_rules]}</span></a></li>
              <li><a id="opt_dlp_files" class="active" href="#"><span>{$translations[$t_dlp_files]}</span></a></li>
              <li><a id="opt_dlp_network_places" href="?module=dlp&action=showNetworkPlaces"><span>{$translations[$t_dlp_network_places]}</span></a></li>
              <li><a id="opt_dlp_applications" href="?module=dlp&action=showApplications"><span>{$translations[$t_dlp_menu_applications]}</span></a></li>
              <li><a id="opt_dlp_confg" href="?module=dlp&action=showAdvanced"><span>{$translations[$t_dlp_advanced]}</span></a></li>
            {elseif $smarty.get.action eq "showNetworkPlaces"}
              <li><a id="opt_dlp_policies" href="?module=dlp&action=show"><span>{$translations[$t_dlp_policies]}</span></a></li>
              <li><a id="opt_dlp_rules" href="?module=dlp&action=showRules"><span>{$translations[$t_dlp_rules]}</span></a></li>
              <li><a id="opt_dlp_files" href="?module=dlp&action=showFiles"><span>{$translations[$t_dlp_files]}</span></a></li>
              <li><a id="opt_dlp_network_places" class="active" href="#"><span>{$translations[$t_dlp_network_places]}</span></a></li>
              <li><a id="opt_dlp_applications" href="?module=dlp&action=showApplications"><span>{$translations[$t_dlp_menu_applications]}</span></a></li>
              <li><a id="opt_dlp_confg" href="?module=dlp&action=showAdvanced"><span>{$translations[$t_dlp_advanced]}</span></a></li>
            {elseif $smarty.get.action eq "showApplications"}
              <li><a id="opt_dlp_policies" href="?module=dlp&action=show"><span>{$translations[$t_dlp_policies]}</span></a></li>
              <li><a id="opt_dlp_rules" href="?module=dlp&action=showRules"><span>{$translations[$t_dlp_rules]}</span></a></li>
              <li><a id="opt_dlp_files" href="?module=dlp&action=showFiles"><span>{$translations[$t_dlp_files]}</span></a></li>
              <li><a id="opt_dlp_network_places" href="?module=dlp&action=showNetworkPlaces" ><span>{$translations[$t_dlp_network_places]}</span></a></li>
              <li><a id="opt_dlp_applications" class="active" href="#"><span>{$translations[$t_dlp_menu_applications]}</span></a></li>
              <li><a id="opt_dlp_confg" href="?module=dlp&action=showAdvanced"><span>{$translations[$t_dlp_advanced]}</span></a></li>              
            {elseif $smarty.get.action eq "showAdvanced"}
              <li><a id="opt_dlp_policies" href="?module=dlp&action=show"><span>{$translations[$t_dlp_policies]}</span></a></li>  
              <li><a id="opt_dlp_rules" href="?module=dlp&action=showRules"><span>{$translations[$t_dlp_rules]}</span></a></li>
              <li><a id="opt_dlp_files" href="?module=dlp&action=showFiles"><span>{$translations[$t_dlp_files]}</span></a></li>
              <li><a id="opt_dlp_network_places" href="?module=dlp&action=showNetworkPlaces"><span>{$translations[$t_dlp_network_places]}</span></a></li>
              <li><a id="opt_dlp_applications" href="?module=dlp&action=showApplications"><span>{$translations[$t_dlp_menu_applications]}</span></a></li>
              <li><a id="opt_dlp_confg" class="active" href="#"><span>{$translations[$t_dlp_advanced]}</span></a></li>
            {else}
              <li><a id="opt_dlp_policies" href="?module=dlp&action=show"><span>{$translations[$t_dlp_policies]}</span></a></li>
              <li><a id="opt_dlp_rules" href="?module=dlp&action=showRules"><span>{$translations[$t_dlp_rules]}</span></a></li>
              <li><a id="opt_dlp_files" href="?module=dlp&action=showFiles"><span>{$translations[$t_dlp_files]}</span></a></li>
              <li><a id="opt_dlp_network_places" href="?module=dlp&action=showNetworkPlaces"><span>{$translations[$t_dlp_network_places]}</span></a></li>
              <li><a id="opt_dlp_applications" href="?module=dlp&action=showApplications"><span>{$translations[$t_dlp_menu_applications]}</span></a></li>
              <li><a id="opt_dlp_confg" href="?module=dlp&action=showAdvanced"><span>{$translations[$t_dlp_advanced]}</span></a></li>
            {/if}

          </ul>
