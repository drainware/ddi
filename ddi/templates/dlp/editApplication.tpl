{ assign var="t_manage_rules" value="Manage Rules" }
{ assign var="t_rule" value="Rule" }
{ assign var="t_short_desc" value="Short Desc" }
{ assign var="t_cancel" value="Cancel" }
{ assign var="t_reset" value="Reset" }
{ assign var="t_save" value="Save" }
{ assign var="t_policies_optional" value="Policies (optional)" }
{ assign var="t_application" value="Application" }

<h2><a href="#">{$software_version}</a></h2>
<fieldset>
    <legend>{$translations[$t_manage_rules]}</legend>
    <div class="form-dlp" id="dlp_edit_application">
        <form action="?module=dlp&action=updateApplication" method="POST" id="edit_application_form">
            <p>
                <input type="hidden" name="id" value="{$application._id}">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_application]}:</span>
                <input type="text" class="text medium required" name="application" value="{$application.application}">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_short_desc]}:</span>
                <input type="text" class="text medium required" name="description" readonly="readonly" value="{$application.description}">
            </p>
            {if $nbr_policies neq 0}
                <p>
                    <span class="entitle_medium">{$translations[$t_policies_optional]}:</span>
                </p>
                <div style="padding-left: 65px;">
                    {foreach from=$policies key=id item=policy}
                        <p>
                            <input type="checkbox" name="policies[]" value="{$id}" {if $policy.checked eq 1}checked="checked"{/if}>
                            <span>{$policy.name}</span>
                        </p>
                    {/foreach}
                </div>
            {/if}
            <div>
                <input type="button" class="button mr_10" onclick="window.location.href='?module=dlp&action=showApplications'" value="{$translations[$t_cancel]}"/> 
                <input type="reset" class="button mr_10" value="{$translations[$t_reset]}"/>
                <input type="submit" class="button red" value="{$translations[$t_save]}" />
            </div>
        </form>
    </div>
</fieldset>

