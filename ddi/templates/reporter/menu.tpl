{ assign var="t_web_filter" value="Web Filter" }
{ assign var="t_block_report" value="Block Report" }
{ assign var="t_access_report" value="Access Report" }
{ assign var="t_console" value="Console" }
{ assign var="t_dlp_events" value="DLP Events" }
{ assign var="t_dlp_events_by_policy" value="DLP Events By Policy" }
{ assign var="t_atp_events" value="ATP Events" }
{ assign var="t_atp_events_by_app" value="ATP Events By Application" }
{ assign var="t_atp_events_by_group" value="ATP Events By Group" }
{ assign var="t_dlp_stats" value="DLP Stats" }
{ assign var="t_sandbox_stats" value="Sandbox Stats" }
{ assign var="t_search_reports" value="Search Reports" }

<br/>
<ul>
    {if $smarty.get.action eq "show" || $smarty.get.action eq ''}
        {if $mstats.webfilter eq 1}
            <li><h1>{$translations[$t_web_filter]}</h1></li>
            <li><a id="opt_stats_access" href="#" class="active"><span>{$translations[$t_access_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showBlocked"><span>{$translations[$t_block_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=console"><span>{$translations[$t_console]}</span></a></li>
        {/if}
        {if $mstats.dlp eq 1}
            <li><h1>DLP</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStats"><span>{$translations[$t_dlp_events]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStatsSlider"><span>{$translations[$t_dlp_stats]}</span></a></li>
        {/if}
        {if $mstats.atp eq 1}
            <li><h1>Sandbox</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStats"><span>{$translations[$t_atp_events]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStatsSlider"><span>{$translations[$t_sandbox_stats]}</span></a></li>
        {/if}
        {if $mstats.forensics eq 1}
            <li><h1>Inspector</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showSearchReports"><span>{$translations[$t_search_reports]}</span></a></li>
        {/if}
    {elseif $smarty.get.action eq "showBlocked"}
        {if $mstats.webfilter eq 1}
            <li><h1>{$translations[$t_web_filter]}</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=show"><span>{$translations[$t_access_report]}</span></a></li>
            <li><a id="opt_stats_access" href="#" class="active"><span>{$translations[$t_block_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=console"><span>{$translations[$t_console]}</span></a></li>
        {/if}
        {if $mstats.dlp eq 1}
            <li><h1>DLP</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStats"><span>{$translations[$t_dlp_events]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStatsSlider"><span>{$translations[$t_dlp_stats]}</span></a></li>
        {/if}
        {if $mstats.atp eq 1}
            <li><h1>Sandbox</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStats"><span>{$translations[$t_atp_events]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStatsSlider"><span>{$translations[$t_sandbox_stats]}</span></a></li>
        {/if}
        {if $mstats.forensics eq 1}
            <li><h1>Inspector</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showSearchReports"><span>{$translations[$t_search_reports]}</span></a></li>
        {/if}
    {elseif $smarty.get.action eq "console"}
        {if $mstats.webfilter eq 1}
            <li><h1>{$translations[$t_web_filter]}</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=show"><span>{$translations[$t_access_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showBlocked"><span>{$translations[$t_block_report]}</span></a></li>
            <li><a id="opt_stats_access" href="#" class="active"><span>{$translations[$t_console]}</span></a></li>
        {/if}
        {if $mstats.dlp eq 1}
            <li><h1>DLP</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStats"><span>{$translations[$t_dlp_events]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStatsSlider"><span>{$translations[$t_dlp_stats]}</span></a></li>
        {/if}
        {if $mstats.atp eq 1}
            <li><h1>Sandbox</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStats"><span>{$translations[$t_atp_events]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStatsSlider"><span>{$translations[$t_sandbox_stats]}</span></a></li>
        {/if}
        {if $mstats.forensics eq 1}
            <li><h1>Inspector</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showSearchReports"><span>{$translations[$t_search_reports]}</span></a></li>
        {/if}
    {elseif $smarty.get.action eq "showDlpStats"}
        {if $mstats.webfilter eq 1}
            <li><h1>{$translations[$t_web_filter]}</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=show"><span>{$translations[$t_access_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showBlocked"><span>{$translations[$t_block_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=console"><span>{$translations[$t_console]}</span></a></li>
        {/if}
        {if $mstats.dlp eq 1}
            <li><h1>DLP</h1></li>
            <li><a id="opt_stats_access" href="#" class="active"><span>{$translations[$t_dlp_events]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStatsSlider"><span>{$translations[$t_dlp_stats]}</span></a></li>
        {/if}
        {if $mstats.atp eq 1}
            <li><h1>Sandbox</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStats"><span>{$translations[$t_atp_events]}</span></a></li>                
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStatsSlider"><span>{$translations[$t_sandbox_stats]}</span></a></li>
        {/if}
        {if $mstats.forensics eq 1}
            <li><h1>Inspector</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showSearchReports"><span>{$translations[$t_search_reports]}</span></a></li>
        {/if}
    {elseif $smarty.get.action eq "showDlpStatsSlider"}
        {if $mstats.webfilter eq 1}
            <li><h1>{$translations[$t_web_filter]}</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=show"><span>{$translations[$t_access_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showBlocked"><span>{$translations[$t_block_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=console"><span>{$translations[$t_console]}</span></a></li>
        {/if}
        {if $mstats.dlp eq 1}
            <li><h1>DLP</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStats"><span>{$translations[$t_dlp_events]}</span></a></li>
            <li><a id="opt_stats_access" href="#" class="active"><span>{$translations[$t_dlp_stats]}</span></a></li>
        {/if}
        {if $mstats.atp eq 1}
            <li><h1>Sandbox</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStats"><span>{$translations[$t_atp_events]}</span></a></li>                
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStatsSlider"><span>{$translations[$t_sandbox_stats]}</span></a></li>
        {/if}
        {if $mstats.forensics eq 1}
            <li><h1>Inspector</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showSearchReports"><span>{$translations[$t_search_reports]}</span></a></li>
        {/if}
    {elseif $smarty.get.action eq "showATPStats"}
        {if $mstats.webfilter eq 1}
            <li><h1>{$translations[$t_web_filter]}</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=show"><span>{$translations[$t_access_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showBlocked"><span>{$translations[$t_block_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=console"><span>{$translations[$t_console]}</span></a></li>
        {/if}
        {if $mstats.dlp eq 1}
            <li><h1>DLP</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStats"><span>{$translations[$t_dlp_events]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStatsSlider"><span>{$translations[$t_dlp_stats]}</span></a></li>
        {/if}
        {if $mstats.atp eq 1}
            <li><h1>Sandbox</h1></li>
            <li><a id="opt_stats_access" href="#" class="active"><span>{$translations[$t_atp_events]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStatsSlider"><span>{$translations[$t_sandbox_stats]}</span></a></li>
        {/if}
        {if $mstats.forensics eq 1}
            <li><h1>Inspector</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showSearchReports"><span>{$translations[$t_search_reports]}</span></a></li>
        {/if}
    {elseif $smarty.get.action eq "showATPStatsSlider"}
        {if $mstats.webfilter eq 1}
            <li><h1>{$translations[$t_web_filter]}</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=show"><span>{$translations[$t_access_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showBlocked"><span>{$translations[$t_block_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=console"><span>{$translations[$t_console]}</span></a></li>
        {/if}
        {if $mstats.dlp eq 1}
            <li><h1>DLP</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStats"><span>{$translations[$t_dlp_events]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStatsSlider"><span>{$translations[$t_dlp_stats]}</span></a></li>
        {/if}
        {if $mstats.atp eq 1}
            <li><h1>Sandbox</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStats"><span>{$translations[$t_atp_events]}</span></a></li>
            <li><a id="opt_stats_access" href="#" class="active"><span>{$translations[$t_sandbox_stats]}</span></a></li>
        {/if}
        {if $mstats.forensics eq 1}
            <li><h1>Inspector</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showSearchReports"><span>{$translations[$t_search_reports]}</span></a></li>
        {/if}
    {elseif $smarty.get.action eq "showSearchReports"}
        {if $mstats.webfilter eq 1}
            <li><h1>{$translations[$t_web_filter]}</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=show"><span>{$translations[$t_access_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showBlocked"><span>{$translations[$t_block_report]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=console"><span>{$translations[$t_console]}</span></a></li>
        {/if}
        {if $mstats.dlp eq 1}
            <li><h1>DLP</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStats"><span>{$translations[$t_dlp_events]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showDlpStatsSlider"><span>{$translations[$t_dlp_stats]}</span></a></li>
        {/if}
        {if $mstats.atp eq 1}
            <li><h1>Sandbox</h1></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStats"><span>{$translations[$t_atp_events]}</span></a></li>
            <li><a id="opt_stats_blocked" href="?module=reporter&action=showATPStatsSlider"><span>{$translations[$t_sandbox_stats]}</span></a></li>
        {/if}
        {if $mstats.forensics eq 1}
            <li><h1>Inspector</h1></li>
            <li><a id="opt_stats_blocked" href="#" class="active"><span>{$translations[$t_search_reports]}</span></a></li>
        {/if}
    {/if}
</ul>
