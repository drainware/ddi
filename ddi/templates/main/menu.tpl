{ assign var="t_information" value="Information" }
{ assign var="t_modules" value="Modules" }
{ assign var="t_filter_type" value="Type of Filter" }
{ assign var="t_license" value="License" }
{ assign var="t_credentials" value="Credentials" }
{ assign var="t_advanced_configuration" value="Advanced Configuration" }
{ assign var="t_reboot" value="Reboot" }
{ assign var="t_reboot_msg" value="Reboot Message" }
{ assign var="t_users" value="user" }
{ assign var="t_groups" value="group" }
{ assign var="t_subscription" value="Subscription" }
{ assign var="t_authentication" value="Authentication" }
{ assign var="t_notifications" value="Notifications" }
{ assign var="t_time_zone" value="Time Zone" }

<ul>
    {if $mode neq "cloud"}
        {if $smarty.get.action eq "show" || $smarty.get.action eq ''}
            <li><a id="opt_info" href="#" class="active"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_modules" href="?module=main&action=showModules"><span>{$translations[$t_modules]}</span></a></li>
            <li><a id="opt_filters" href="?module=main&action=showTypeFilter"><span>{$translations[$t_filter_type]}</span></a></li>
            <li><a id="opt_license" href="?module=main&action=showLicense"><span>{$translations[$t_license]}</span></a></li>
            <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="?module=main&action=showAdvanced"><span>{$translations[$t_advanced_configuration]}</span></a></li>
            <li><a id="opt_reboot" onclick="if (confirm('{$translations[$t_reboot_msg]}')) location.href='?module=main&action=reboot'; else location.href='';"><span>{$translations[$t_reboot]}</span></a></li>
        {elseif $smarty.get.action eq "showModules"}
            <li><a id="opt_info" href="?module=main&action=show"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_modules" href="#" class="active"><span>{$translations[$t_modules]}</span></a></li>
            <li><a id="opt_filters" href="?module=main&action=showTypeFilter"><span>{$translations[$t_filter_type]}</span></a></li>
            <li><a id="opt_license" href="?module=main&action=showLicense"><span>{$translations[$t_license]}</span></a></li>
            <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="?module=main&action=showAdvanced"><span>{$translations[$t_advanced_configuration]}</span></a></li>
            <li><a id="opt_reboot" onclick="if (confirm('{$translations[$t_reboot_msg]}')) location.href='?module=main&action=reboot'; else location.href='';"><span>{$translations[$t_reboot]}</span></a></li>
        {elseif $smarty.get.action eq "showTypeFilter"}
            <li><a id="opt_info" href="?module=main&action=show"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_modules" href="?module=main&action=showModules"><span>{$translations[$t_modules]}</span></a></li>
            <li><a id="opt_filters" class="active" href="#"><span>{$translations[$t_filter_type]}</span></a></li>
            <li><a id="opt_license" href="?module=main&action=showLicense"><span>{$translations[$t_license]}</span></a></li>
            <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="?module=main&action=showAdvanced"><span>{$translations[$t_advanced_configuration]}</span></a></li>
            <li><a id="opt_reboot" onclick="if (confirm('{$translations[$t_reboot_msg]}')) location.href='?module=main&action=reboot'; else location.href='';"><span>{$translations[$t_reboot]}</span></a></li>
        {elseif $smarty.get.action eq "showLicense"}
            <li><a id="opt_info" href="?module=main&action=show"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_modules" href="?module=main&action=showModules"><span>{$translations[$t_modules]}</span></a></li>
            <li><a id="opt_filters" href="?module=main&action=showTypeFilter"><span>{$translations[$t_filter_type]}</span></a></li>
            <li><a id="opt_license" class="active" href="#"><span>{$translations[$t_license]}</span></a></li>
            <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="?module=main&action=showAdvanced"><span>{$translations[$t_advanced_configuration]}</span></a></li>
            <li><a id="opt_reboot" onclick="if (confirm('{$translations[$t_reboot_msg]}')) location.href='?module=main&action=reboot'; else location.href='';"><span>{$translations[$t_reboot]}</span></a></li>
        {elseif $smarty.get.action eq "showCredentials"}
            <li><a id="opt_info" href="?module=main&action=show"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_modules" href="?module=main&action=showModules"><span>{$translations[$t_modules]}</span></a></li>
            <li><a id="opt_filters" href="?module=main&action=showTypeFilter"><span>{$translations[$t_filter_type]}</span></a></li>
            <li><a id="opt_license" href="?module=main&action=showLicense"><span>{$translations[$t_license]}</span></a></li>
            <li><a id="opt_credentials" class="active" href="#"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="?module=main&action=showAdvanced"><span>{$translations[$t_advanced_configuration]}</span></a></li>
            <li><a id="opt_reboot" onclick="if (confirm('{$translations[$t_reboot_msg]}')) location.href='?module=main&action=reboot'; else location.href='';"><span>{$translations[$t_reboot]}</span></a></li>                
        {elseif $smarty.get.action eq "showAdvanced"}
            <li><a id="opt_info" href="?module=main&action=show"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_modules" href="?module=main&action=showModules"><span>{$translations[$t_modules]}</span></a></li>
            <li><a id="opt_filters" href="?module=main&action=showTypeFilter"><span>{$translations[$t_filter_type]}</span></a></li>
            <li><a id="opt_license" href="?module=main&action=showLicense"><span>{$translations[$t_license]}</span></a></li>
            <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" class="active" href="#"><span>{$translations[$t_advanced_configuration]}</span></a></li>
            <li><a id="opt_reboot" onclick="if (confirm('{$translations[$t_reboot_msg]}')) location.href='?module=main&action=reboot'; else location.href='';"><span>{$translations[$t_reboot]}</span></a></li>        
        {else}
            <li><a id="opt_info" href="?module=main&action=show"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_modules" href="?module=main&action=showModules"><span>{$translations[$t_modules]}</span></a></li>
            <li><a id="opt_filters" href="?module=main&action=showTypeFilter"><span>{$translations[$t_filter_type]}</span></a></li>
            <li><a id="opt_license" href="?module=main&action=showLicense"><span>{$translations[$t_license]}</span></a></li>
            <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="?module=main&action=showAdvanced"><span>{$translations[$t_advanced_configuration]}</span></a></li>
            <li><a id="opt_reboot" onclick="if (confirm('{$translations[$t_reboot_msg]}')) location.href='?module=main&action=reboot'; else location.href='';"><span>{$translations[$t_reboot]}</span></a></li>
        {/if}
    {else}
        {if $smarty.get.action eq "show" || $smarty.get.action eq ''} 
            <li><a id="opt_info" href="#" class="active"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="?module=main&action=showCloudConfig"><span>{$translations[$t_subscription]}</span></a></li>
	    <li><a id="opt_groups" href="?module=group"><span>{$translations[$t_groups]}</span></a></li>
            <li><a id="opt_users" href="?module=user"><span>{$translations[$t_users]}</span></a></li>
            <li><a id="opt_authentication" href="?module=main&action=showUserAuth"><span>{$translations[$t_authentication]}</span></a></li>
            <li><a id="opt_notifications" href="?module=main&action=showNotifications"><span>{$translations[$t_notifications]}</span></a></li>
            <li><a id="opt_timezone" href="?module=main&action=showTimeZone"><span>{$translations[$t_time_zone]}</span></a></li>
        {elseif $smarty.get.action eq "showInviteFriend"}
            <li><a id="opt_info" href="?module=main&action=show"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="?module=main&action=showCloudConfig"><span>{$translations[$t_subscription]}</span></a></li>
	    <li><a id="opt_groups" href="?module=group"><span>{$translations[$t_groups]}</span></a></li>
            <li><a id="opt_users" href="?module=user"><span>{$translations[$t_users]}</span></a></li>
            <li><a id="opt_authentication" href="?module=main&action=showUserAuth"><span>{$translations[$t_authentication]}</span></a></li>
            <li><a id="opt_notifications" href="?module=main&action=showNotifications"><span>{$translations[$t_notifications]}</span></a></li>
            <li><a id="opt_timezone" href="?module=main&action=showTimeZone"><span>{$translations[$t_time_zone]}/span></a></li>
        {elseif $smarty.get.action eq "showCredentials"}
            <li><a id="opt_info" href="?module=main&action=show"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_credentials" href="#" class="active"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="?module=main&action=showCloudConfig"><span>{$translations[$t_subscription]}</span></a></li>
            <li><a id="opt_groups" href="?module=group"><span>{$translations[$t_groups]}</span></a></li>
            <li><a id="opt_users" href="?module=user"><span>{$translations[$t_users]}</span></a></li>
            <li><a id="opt_authentication" href="?module=main&action=showUserAuth"><span>{$translations[$t_authentication]}</span></a></li>
            <li><a id="opt_notifications" href="?module=main&action=showNotifications"><span>{$translations[$t_notifications]}</span></a></li>
            <li><a id="opt_timezone" href="?module=main&action=showTimeZone"><span>{$translations[$t_time_zone]}</span></a></li>
        {elseif $smarty.get.action eq "showCloudConfig"} 
            <li><a id="opt_info" href="?module=main&action=show"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="#" class="active"><span>{$translations[$t_subscription]}</span></a></li>
            <li><a id="opt_groups" href="?module=group"><span>{$translations[$t_groups]}</span></a></li>
            <li><a id="opt_users" href="?module=user"><span>{$translations[$t_users]}</span></a></li>
            <li><a id="opt_authentication" href="?module=main&action=showUserAuth"><span>{$translations[$t_authentication]}</span></a></li>
            <li><a id="opt_notifications" href="?module=main&action=showNotifications"><span>{$translations[$t_notifications]}</span></a></li>
            <li><a id="opt_timezone" href="?module=main&action=showTimeZone"><span>{$translations[$t_time_zone]}</span></a></li>
        {elseif $smarty.get.action eq "showUserAuth"} 
            <li><a id="opt_info" href="?module=main&action=show"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="?module=main&action=showCloudConfig"><span>{$translations[$t_subscription]}</span></a></li>
            <li><a id="opt_groups" href="?module=group"><span>{$translations[$t_groups]}</span></a></li>
            <li><a id="opt_users" href="?module=user"><span>{$translations[$t_users]}</span></a></li>
            <li><a id="opt_authentication" href="#" class="active"><span>{$translations[$t_authentication]}</span></a></li>
            <li><a id="opt_notifications" href="?module=main&action=showNotifications"><span>{$translations[$t_notifications]}</span></a></li>
            <li><a id="opt_timezone" href="?module=main&action=showTimeZone"><span>{$translations[$t_time_zone]}</span></a></li>
        {elseif $smarty.get.action eq "showNotifications"} 
            <li><a id="opt_info" href="?module=main&action=show"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="?module=main&action=showCloudConfig"><span>{$translations[$t_subscription]}</span></a></li>
            <li><a id="opt_groups" href="?module=group"><span>{$translations[$t_groups]}</span></a></li>
            <li><a id="opt_users" href="?module=user"><span>{$translations[$t_users]}</span></a></li>
            <li><a id="opt_authentication" href="?module=main&action=showUserAuth"><span>{$translations[$t_authentication]}</span></a></li>
            <li><a id="opt_notifications" href="#" class="active"><span>{$translations[$t_notifications]}</span></a></li>
            <li><a id="opt_timezone" href="?module=main&action=showTimeZone"><span>{$translations[$t_time_zone]}</span></a></li>
        {elseif $smarty.get.action eq "showTimeZone"}
            <li><a id="opt_info" href="?module=main&action=show"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="?module=main&action=showCloudConfig"><span>{$translations[$t_subscription]}</span></a></li>
            <li><a id="opt_groups" href="?module=group"><span>{$translations[$t_groups]}</span></a></li>
            <li><a id="opt_users" href="?module=user"><span>{$translations[$t_users]}</span></a></li>
            <li><a id="opt_authentication" href="?module=main&action=showUserAuth"><span>{$translations[$t_authentication]}</span></a></li>
            <li><a id="opt_notifications" href="?module=main&action=showNotifications"><span>{$translations[$t_notifications]}</span></a></li>
            <li><a id="opt_timezone" href="#" class="active"><span>{$translations[$t_time_zone]}</span></a></li>
        {else}
            <li><a id="opt_info" href="?module=main&action=show"><span>{$translations[$t_information]}</span></a></li>
            <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
            <li><a id="opt_configuration" href="?module=main&action=showCloudConfig"><span>{$translations[$t_subscription]}</span></a></li>
            <li><a id="opt_groups" href="?module=group"><span>{$translations[$t_groups]}</span></a></li>
            <li><a id="opt_users" href="?module=user"><span>{$translations[$t_users]}</span></a></li>
            <li><a id="opt_authentication" href="?module=main&action=showUserAuth"><span>{$translations[$t_authentication]}</span></a></li>
            <li><a id="opt_notifications" href="?module=main&action=showNotifications"><span>{$translations[$t_notifications]}</span></a></li>
            <li><a id="opt_timezone" href="?module=main&action=showTimeZone"><span>{$translations[$t_time_zone]}</span></a></li>
        {/if}
    {/if}   
</ul>
