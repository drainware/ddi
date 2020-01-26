{ assign var="manage_files" value="Manage Files" }
{ assign var="dlp_files" value="Files" }
{ assign var="new_file" value="New File" }
{ assign var="edit" value="Edit" }
{ assign var="v_groups" value="group" }
{ assign var="remove" value="Remove" }
{ assign var="save" value="Save" }
{ assign var="reset" value="Reset" }
{ assign var="cancel" value="Cancel" }
{ assign var="t_name" value="Name" }
{ assign var="t_policies_optional" value="Policies (optional)" }

<h2><a href="#">{$software_version}</a></h2>
<fieldset>
    <legend>{$translations[$manage_files]}</legend>
    <div class="form-dlp" id="dlp_edit_file">
        <form action="" method="get" id="edit_file_form">
            <p>
                <input type="hidden" name="module" value="dlp">
                <input type="hidden" name="action" value="updateFile">
                <input type="hidden" name="id" value="{$file._id}">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_name]}:</span>
                <input type="text" class="text medium required" name="name" id="name" value="{$file.name}">
            </p>
            {if $nbr_policies neq 0}
                <p>
                    <span class="entitle_medium">{$translations[$t_policies_optional]}:</span>
                </p>
                <div style="padding-left: 65px;" id="margen-reglas">
                    {foreach from=$policies key=id item=policy}
                        <p>
                            <input type="checkbox" name="policies[]" value="{$id}" {if $policy.checked eq 1}checked="checked"{/if}>
                            <span>{$policy.name}</span>
                        </p>
                    {/foreach}
                </div>
            {/if}
            <div>
                <input type="button" class="button mr_10" onclick="window.location.href='?module=dlp&action=showFiles'" value="{$translations[$cancel]}"/>
                <input type="reset" class="button mr_10" value="{$translations[$reset]}"/>
                <input type="submit" class="button red" value="{$translations[$save]}" />
            </div>   
        </form>
    </div>
</fieldset>

