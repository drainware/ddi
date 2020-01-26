{ assign var="manage_rules" value="Manage Rules" }
{ assign var="dlp_rules" value="Rules" }
{ assign var="new_rule" value="New Rule" }
{ assign var="edit" value="Edit" }
{ assign var="remove" value="Remove" }
{ assign var="t_applications" value="Applications" }
{ assign var="t_help_box_applications" value="Help Box Applications" }
{ assign var="t_new_applications" value="New Applications" }

<h2><a href="#">{$software_version}</a></h2>
<fieldset>
    <legend>{$translations[$t_applications]}</legend>
    <div id="dlp_rules">
<div class="help_box">
    {$translations[$t_help_box_applications]}
</div>
        {foreach from=$applications item=application}
            <div class="element" id="{$application._id}">
                <div class="info">
                    <div class="element-name">{$application.description}</div>
                </div>
                <div class="controls">
                    {if $application.editable}
                        <a class="button mr_10 ml_10" href="?module=dlp&action=editApplication&id={$application._id}">{$translations[$edit]}</a>
                        <a class="button red" href="?module=dlp&action=deleteApplication&id={$application._id}">{$translations[$remove]}</a>
                    {/if}
                </div>
            </div>
        {/foreach}
    </div>
</fieldset>

<div class="button_container">
    <a class="button" href="?module=dlp&action=newApplication">{$translations[$t_new_applications]}</a>
</div>

