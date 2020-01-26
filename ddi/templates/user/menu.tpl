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
    <li><a id="opt_info" href="?module=main"><span>{$translations[$t_information]}</span></a></li>
    <li><a id="opt_credentials" href="?module=main&action=showCredentials"><span>{$translations[$t_credentials]}</span></a></li>
    <li><a id="opt_configuration" href="?module=main&action=showCloudConfig"><span>{$translations[$t_subscription]}</span></a></li>
    <li><a id="opt_groups" href="?module=group" ><span>{$translations[$t_groups]}</span></a></li>
    <li><a id="opt_users" href="?module=user" class="active"><span>{$translations[$t_users]}</span></a></li>
    <li><a id="opt_authentication" href="?module=main&action=showUserAuth"><span>{$translations[$t_authentication]}</span></a></li>
    <li><a id="opt_notifications" href="?module=main&action=showNotifications"><span>{$translations[$t_notifications]}</span></a></li>
    <li><a id="opt_timezone" href="?module=main&action=showTimeZone"><span>{$translations[$t_time_zone]}</span></a></li>
</ul>