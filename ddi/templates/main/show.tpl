{ assign var="general_configuration" value="General Configuration" }
{ assign var="information" value="Information" }
{ assign var="cpu_usage" value="Cpu usage" }
{ assign var="memory_usage" value="Memory usage" }
{ assign var="swap_usage" value="Swap usage" }
{ assign var="disk_usage" value="Disk usage" }
{ assign var="network_connection" value="Network connection" }
{ assign var="web_filter_module" value="Web filter module" }
{ assign var="dlp_module" value="Data Loss Prevention module" }
{ assign var="connections_web_filter" value="Connections to Web Filter" }
{ assign var="connections_admin" value="Connections to Admin" }
{ assign var="t_antivirus" value="Antivirus" }
{ assign var="t_adv" value="Advertising Control" }
{ assign var="t_limit_monthly_box" value="Limit of monthly events"}
{ assign var="t_limit_download_software" value="Download endpoint software"}
{ assign var="t_download_installer_windows" value="Download installer for Microsoft Windows"}
{ assign var="t_get_free_event" value="Get free events!"}
{ assign var="t_view_details" value="View Details"}
{ assign var="t_refer_people" value="Refer people to Drainware"}
{ assign var="t_monthly_dlp_event" value="Monthly DLP Events"}
{ assign var="t_global_dlp_event" value="Global DLP Events"}
{ assign var="t_monthly_sandbox_event" value="Monthly Sandbox Events"}
{ assign var="t_global_sandbox_event" value="Global Sandbox Events"}
{ assign var="t_monthly_inspector_event" value="Monthly Inspector Events"}
{ assign var="t_global_inspector_event" value="Global Inspector Events"}
{ assign var="t_monthly_event" value="Monthly Events"}
{ assign var="t_global_event" value="Global Events"}
{ assign var="t_imported_group" value="Imported Groups"}
{ assign var="t_monthly_webfilter_blocks_events" value="Monthly Webfilter Blocks Events" }
{ assign var="t_global_webfilter_blocks_events" value="Global Webfilter Blocks Events" }
{ assign var="t_license" value="License" }


{literal}
    <style>
        #gridster{
            display:none;
        }
    </style>
{/literal}

{assign var=trak_username value="@"|explode:$acccount_mail}

<script type="text/javascript">
trak.io.identify({ldelim}
    name: "{$trak_username[0]}",
    email: "{$acccount_mail}",
    account_id: "{$smarty.session.license}",
    headline: "{$client_type}",
    company: "{$account_company}"
  {rdelim})
</script>

<div id="content_info">
    <h3>{$translations[$t_limit_download_software]}</h3>

    <div class="content_section">
        <div class="download_box1">
            <ul>
                <li><a href="http://update.drainware.com/dse/DseWebSetup.exe">{$translations[$t_download_installer_windows]}</a></li>
            </ul>
        </div> 

        <div class="license_box">
            <p>{$translations[$t_license]}: {$license}</p>
        </div>
        <div class="download_box">
            <ul>
                <li><a href="http://www.drainware.com/manual/Drainware_Manual__20131216.pdf" target="_blank">Manual</a></li>
            </ul>
        </div>
    </div>

    <h2><a href="#">{$software_version}</a></h2>
</div>

<!--<div class="system-dialog">
  <span>Dialogo de sistema</span>
  <span class="close-dialog">x</span>
</div>-->

<div id="info" class="content_section">
    <h3>{$translations[$information]}</h3>



    <div id="gridster" class="gridster">
        <ul>
            {if $mode neq 'cloud'}
                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating onoff">
                        <div>{$translations[$dlp_module]}: <span {if $dlpstatus eq "on"} class="activated" {else} class="desactivated" {/if}>{$dlpstatus}</span></div>
                        <div>{$translations[$t_antivirus]}: <span {if $avstatus eq "on"} class="activated" {else} class="desactivated" {/if}>{$avstatus}</span></div>
                        <div>AlienVault Feed: <span {if $mlwstatus eq "on"} class="activated" {else} class="desactivated" {/if}>{$mlwstatus}</span></div>
                    </div>
                </li>

                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating onoff">
                    <!--<div>{$translations[$network_connection]}: <span class="activated">on</span></div>     -->
                        <div>{$translations[$web_filter_module]}: <span {if $wfstatus eq "on"} class="activated" {else} class="desactivated" {/if}>{$wfstatus}</span></div>
                        <div>{$translations[$t_adv]}: <span {if $advstatus eq "on"} class="activated" {else} class="desactivated" {/if}>{$advstatus}</span></div>
                        <div>Anti-phising: <span {if $pshstatus eq "on"} class="activated" {else} class="desactivated" {/if}>{$pshstatus}</span></div>
                    </div>
                </li>


                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating">
                        <div class="graphcont"><div>{$translations[$cpu_usage]}</div>
                            <div class="graph" id="cpuusagebar"><strong class="bar" style="width:50%">{$translations[$t_loading]}</strong></div>
                        </div>
                    </div>
                </li>

                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating">
                        <div class="graphcont"><div>{$translations[$memory_usage]}</div>
                            <div class="graph" id="memusagebar"><strong class="bar" style="width:50%">{$translations[$t_loading]}</strong></div>
                        </div>
                    </div>
                </li>

                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating">
                        <div class="graphcont"><div>{$translations[$swap_usage]}</div>
                            <div class="graph" id="swapusagebar"><strong class="bar" style="width:50%">{$translations[$t_loading]}</strong></div>
                        </div>
                    </div>
                </li> 

                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating">
                        <div class="graphcont"><div>{$translations[$disk_usage]}</div>
                            <div class="graph" id="diskusagebar"><strong class="bar" style="width:50%">{$translations[$t_loading]}</strong></div>
                        </div>
                    </div>
                </li> 
            {/if}

            {if $client_type eq 'freemium'}
                <li data-row="1" data-col="1" data-sizex="2" data-sizey="1" class="gs_w">
                    <div class="rating">
                        <div class="graphcont">
                            <div>{$translations[$t_limit_monthly_box]}</div>
                            <div class="graph" id="eventsusagebar"><strong class="bar" style="width:50%">{$translations[$t_loading]}</strong></div>
                        </div>
                    </div>
                </li>
                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating number">
                        <div class="graphcont alternate"  >
                            <div>{$translations[$t_get_free_event]}</div>
                            <a href="/ddi/?module=main&action=showInviteFriend">{$translations[$t_refer_people]}</a>
                        </div>
                    </div>
                </li>
            {/if}

            {if $active_module.webfilter eq 1}
                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating number">

                        <div id="monthly_wf_stats" class="num-box"><img src="images/loading.gif"></div>
                        <div class="graphcont">
                            <a href="/ddi/?module=reporter&action=console">{$translations[$t_monthly_webfilter_blocks_events]}</a>
                        </div>
                    </div>
                </li>
                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating number">
                        <div id="global_wf_stats" class="num-box"><img src="images/loading.gif"></div>
                        <div class="graphcont">
                            {$translations[$t_global_webfilter_blocks_events]}
                            <a href="/ddi/?module=reporter&action=console">{$translations[$t_view_details]}</a>
                        </div>
                    </div>
                </li>                
            {/if}

            {if $active_module.dlp eq 1}    
                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating number">
						<div id="monthly_dlp_stats" class="num-box"><img src="images/loading.gif"></div>
                        <div class="graphcont">
                            {$translations[$t_monthly_dlp_event]}
							<a href="/ddi/?module=reporter&action=showDlpStats&date%5Bstart%5D={$event_date.first}&date%5Bend%5D={$event_date.last}">
							{$translations[$t_view_details]}</a>
                        </div>
						
                    </div>
                </li>
                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating number">
                        
						<div id="global_dlp_stats" class="num-box"><img src="images/loading.gif"></div>
                        <div class="graphcont">
							{$translations[$t_global_dlp_event]}
							<a href="/ddi/?module=reporter&action=showDlpStats">{$translations[$t_view_details]}</a>
                        </div>
                    </div>
                </li>
            {/if}

            {if $active_module.atp eq 1}
                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating number">
						<div id="monthly_atp_stats" class="num-box"><img src="images/loading.gif"></div>
                        <div class="graphcont">
						{$translations[$t_monthly_sandbox_event]}
                        <a href="/ddi/?module=reporter&action=showATPStats&date%5Bstart%5D={$event_date.first}&date%5Bend%5D={$event_date.last}">
						{$translations[$t_view_details]}</a>
                        </div>
                    </div>
                </li>
                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating number">
						<div id="global_atp_stats" class="num-box"><img src="images/loading.gif"></div>
                        <div class="graphcont">
							{$translations[$t_global_sandbox_event]}
							<a href="/ddi/?module=reporter&action=showATPStats">
							{$translations[$t_view_details]}</a>
                        </div>
                    </div>
                </li>
            {/if}

            {if $active_module.forensics eq 1}
                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating number">
                        
						<div id="monthly_forensics_stats" class="num-box"><img src="images/loading.gif"></div>
						<div class="graphcont">
							{$translations[$t_monthly_inspector_event]}
							<a href="#">{$translations[$t_view_details]}</a>
						</div>
                    </div>
                </li>
                <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                    <div class="rating number">                        
						<div id="global_forensics_stats" class="num-box"><img src="images/loading.gif"></div>
                        <div class="graphcont">
                        {$translations[$t_global_inspector_event]}
						<a href="#">{$translations[$t_view_details]}</a>
                        </div>
                    </div>
                </li>
            {/if}

            <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                <div class="rating number">
					<div id="monthly_general_stats" class="num-box"><img src="images/loading.gif"></div>
                    <div class="graphcont">
					{$translations[$t_monthly_event]}
						<a href="/ddi/?module=reporter">
						{$translations[$t_view_details]}</a>
                    </div>
                </div>
            </li>
            <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                <div class="rating number">
					<div id="global_general_stats" class="num-box"><img src="images/loading.gif"></div>
                    <div class="graphcont">
					{$translations[$t_global_event]}
					<a href="/ddi/?module=reporter">
						{$translations[$t_view_details]}</a>
                    </div>
                </div>
            </li>
            
            <li data-row="1" data-col="1" data-sizex="1" data-sizey="1" class="gs_w">
                <div class="rating number">
					<div id="ngroups" class="num-box"><img src="images/loading.gif"></div>
                    <div class="graphcont">
						{$translations[$t_imported_group]}
						<a href="/ddi/?module=group">
						{$translations[$t_view_details]}</a>
                    </div>
                </div>
            </li>

        </ul>
    </div>

    <!--
    <div class="button_container">
    <a class="button" id="add_widget">Add widget</a>
    </div>
    -->


</div>

