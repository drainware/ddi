{ assign var="t_general_configuration" value="General Configuration" }
{ assign var="t_credentials" value="Credentials" }
{ assign var="t_admin_user" value="Admin User" }
{ assign var="t_password" value="Password" }
{ assign var="t_repeat_please" value="Repeat, please" }
{ assign var="t_save" value="Save" }
{ assign var="t_notifications" value="Notifications" }
{ assign var="t_status" value="Status" }
{ assign var="t_action" value="Action" }
{ assign var="t_severity" value="Severity" }
{ assign var="t_enabled" value="enabled" }
{ assign var="t_disabled" value="disabled" }
{ assign var="t_block" value="block" }
{ assign var="t_alert" value="alert" }
{ assign var="t_log" value="log" }
{ assign var="t_high" value="high" }
{ assign var="t_medium" value="medium" }
{ assign var="t_low" value="low" }
{ assign var="t_enabled_notifications_browser" value="Enabled notifications browser" }

<div id="content_info">
    <h2><a href="#">{$software_version}</a></h2>
</div>

<form action="?module=main&action=saveNotifications" method="POST" enctype="multipart/form-data">
    <fieldset>
        <legend>{$translations[$t_notifications]}</legend>
        <p>
            <span class="entitle_medium">{$translations[$t_status]}</span>
            <span>
                {* Verify last check in all browsers *}
                <input type="radio" name="status" value="enabled" {if $notifications.status == "enabled"} checked="checked" {/if} id="notification_enabled" /> <label for="">{$translations[$t_enabled]}</label>
                <input type="radio" name="status" value="disabled" {if $notifications.status != "enabled"} checked="checked" {/if} id="notification_disabled" /> <label for="">{$translations[$t_disabled]}</label>
            </span>
        </p>
        <div id="notify_when">
            <p>
                <span class="entitle_medium">{$translations[$t_action]}</span>
                <span>
                    <input type="checkbox" name="when[action][b]" value="block" {if $notifications.when.action.b} checked="checked" {/if}><label for="">{$translations[$t_block]}</label>
                    <input type="checkbox" name="when[action][a]" value="alert" {if $notifications.when.action.a} checked="checked" {/if}><label for="">{$translations[$t_alert]}</label>
                    <input type="checkbox" name="when[action][l]" value="log" {if $notifications.when.action.l} checked="checked" {/if}><label for="">{$translations[$t_log]}</label>
                </span>
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_severity]}</span>
                <span>
                    <input type="checkbox" name="when[severity][h]" value="high" {if $notifications.when.severity.h} checked="checked" {/if}><label for="">{$translations[$t_high]}</label>
                    <input type="checkbox" name="when[severity][m]" value="medium" {if $notifications.when.severity.m} checked="checked" {/if}><label for="">{$translations[$t_medium]}</label>
                    <input type="checkbox" name="when[severity][l]" value="low" {if $notifications.when.severity.l} checked="checked" {/if}><label for="">{$translations[$t_low]}</label>
                </span>
            </p>
        </div>
                
    </fieldset>
    <input id="browser_permission" class="button" type=button value="{$translations[$t_enabled_notifications_browser]}" />
    <input class="button red" type="submit" value="{$translations[$t_save]}" />
</form>
