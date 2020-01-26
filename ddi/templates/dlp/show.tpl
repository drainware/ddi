{ assign var="t_manage_policies" value="Manage Policies" }
{ assign var="t_policies" value="Policies" }
{ assign var="t_edit" value="Edit" }
{ assign var="t_remove" value="Remove" }
{ assign var="t_new_policy" value="New Policy" }
{ assign var="t_dlp_help" value="Dlp help"}
{ assign var="t_configuration" value="Configuration" }
{ assign var="t_action" value="Action" }

<h2><a href="#">{$software_version}</a></h2>



<fieldset>
    <legend>{$translations[$t_policies]}</legend>
    <div id="dlp_policies">

<div class="help_box">
    {$translations[$t_dlp_help]}
 </div>

        {foreach from=$policies item=policy}
            <div class="element" id="{$policy._id}">
                <div class="info">
                    <div class="element-name">{$policy.name}</div>
                </div>
                <div class="controls">
                    <form action="" method="get">
                        <input type="hidden" name="idPolicy" value="{$policy.id}">
                        <input type="hidden" name="module" value="element">
                        <input type="hidden" name="action" value="block">
                        <a class="button mr_10" href="?module=dlp&action=editPolicy&idPolicy={$policy._id}&id=2">{$translations[$t_configuration]}</a>
                        <a class="button mr_10" href="?module=dlp&action=editPolicy&idPolicy={$policy._id}&id=3">{$translations[$t_action]}</a>
                        <a class="button red" href="?module=dlp&action=deletePolicy&idPolicy={$policy._id}">{$translations[$t_remove]}</a>
                    </form>
                </div>
            </div>
        {/foreach}
    </div>
</fieldset>
    
<div class="button_container">
    <a class="button" href="?module=dlp&action=newPolicy">{$translations[$t_new_policy]}</a>
</div>

