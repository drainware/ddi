{ assign var="manage_rules" value="Manage Rules" }
{ assign var="dlp_rules" value="Rules" }
{ assign var="new_rule" value="New Rule" }
{ assign var="edit" value="Edit" }
{ assign var="remove" value="Remove" }
{ assign var="t_help_box_rules" value="Help Box Rules" }

<h2><a href="#">{$software_version}</a></h2>


<fieldset>
    <legend>{$translations[$dlp_rules]}</legend>
    <div id="dlp_rules">
<div class="help_box">
    {$translations[$t_help_box_rules]}
</div>
        {foreach from=$rules item=rule}
            <div class="element" id="{$rule._id}">
                <div class="info">
                    <div class="element-name">{$rule.description}</div>
                </div>
                <div class="controls">
                    <form action="" method="get">
                        <input type="hidden" name="idRule" value="{$rule.id}">
                        <input type="hidden" name="module" value="element">
                        <input type="hidden" name="action" value="block">
                        <a class="button mr_10 ml_10" href="?module=dlp&action=editRule&idRule={$rule._id}">{$translations[$edit]}</a>
                        <a class="button red" href="?module=dlp&action=deleteRule&idRule={$rule._id}">{$translations[$remove]}</a>
                    </form>
                </div>
            </div>
        {/foreach}
    </div>
</fieldset>

<div class="button_container">
    <a class="button" href="?module=dlp&action=newRule">{$translations[$new_rule]}</a>
</div>

