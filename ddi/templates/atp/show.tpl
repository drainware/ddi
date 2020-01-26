{ assign var="t_manage_applications" value="Manage Applications" }
{ assign var="t_applications" value="Applications" }
{ assign var="t_new_application" value="New Application" }
{ assign var="t_active" value="Active" }
{ assign var="t_inactive" value="Inactive" }
{ assign var="t_edit" value="Edit" }
{ assign var="t_remove" value="Remove" }
{ assign var="t_atp_help" value="Atp help"}

<h2><a href="#">{$software_version}</a></h2>

<fieldset>
    <legend>{$translations[$t_applications]}</legend>
    <div id="atp_rules">
<div class="help_box">
    {$translations[$t_atp_help]}
</div>

        {foreach from=$apps item=app}
            <div class="element" id="{$app._id}">
                <div class="info">
                    <div class="element-name">{$app.description}</div>
                </div>
                <div class="controls">                    
                    {if $app.status}
                        <a class="button green" href="?module=atp&action=changeAppStatus&app_id={$app._id}&app_status=0">{$translations[$t_active]}</a>
                    {else}
                        <a class="button orange" href="?module=atp&action=changeAppStatus&app_id={$app._id}&app_status=1">{$translations[$t_inactive]}</a>
                    {/if}
                    
{*
                    {if $app.general eq 1}
                    <a class="button mr_10 ml_10" href="?module=atp&action=editApp&app_id={$app._id}">{$translations[$t_edit]}</a>
                    {/if}
*}

{*
                    {if not $app.general}
                        <a class="button red" href="?module=atp&action=deleteApp&app_id={$app._id}">{$translations[$t_remove]}</a>
                    {/if}
*}
                </div>
            </div>
        {/foreach}
    </div>
</fieldset>

{*
<div class="button_container">
    <a class="button" href="?module=atp&action=newApp">{$translations[$t_new_application]}</a>
</div>
*}

