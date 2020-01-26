{ assign var="manage_files" value="Manage Files" }
{ assign var="dlp_files" value="Files" }
{ assign var="new_file" value="New File" }
{ assign var="edit" value="Edit" }
{ assign var="remove" value="Remove" }
{ assign var="t_help_box_files" value="Help Box Files" }
{ assign var="t_cant_upload_files" value="cant upload files" }

<h2><a href="#">{$software_version}</a></h2>

<fieldset>
    <legend>{$translations[$dlp_files]}</legend>
    <div id="dlp_files">

<div class="help_box">
{$translations[$t_help_box_files]}
</div>

        {foreach from=$files item=file}            
            <div class="element" id="{$file._id}">
                <div class="info">
                    <div class="element-name">{$file.name}</div>
                </div>
                <div class="controls">
                    <form action="" method="get">
                        <input type="hidden" name="idFile" value="{$file.id}" />
                        <input type="hidden" name="module" value="element" />
                        <input type="hidden" name="action" value="block" />
                        <a class="button mr_10 ml_10" href="?module=dlp&action=editFile&idFile={$file._id}">{$translations[$edit]}</a>
                        <a class="button red" href="?module=dlp&action=deleteFile&idFile={$file._id}">{$translations[$remove]}</a>
                    </form>
                </div>
            </div>
        {/foreach}
    </div>
</fieldset>

{*
<div style="display:none">
    <a id="launch_new_files" class="inline cboxElement" href="#new_files"></a>
    <div id="new_files" class="form-dlp" style="padding: 10px; margin: -10px;width: 580px; background:#fff;">
        <fieldset>
            <legend>{$translations[$dlp_files]}</legend>
            <div id="file_list">
                {foreach from=$new_files key=file_id item=file_object}

                    <div id="{$file_id}">
                        <form action="" method="get">
                            <p>
                                <input type="hidden" name="module" value="dlp">
                                <input type="hidden" name="action" value="updateFile">
                                <input type="hidden" name="id" value="{$file_id}">
                                <input type="hidden" name="name" value="{$file_object.name}">
                            </p>
                            <p>
                            <p class="file-element">{$file_object.name}</p>
                            <div class="file-policies" id="policies-{$file_id}">
                                <ul>
                                    {foreach from=$file_policies[$file_id] key=id item=policy}
                                        <li>
                                            <input type="checkbox" name="policies[]" value="{$id}" {if $policy.checked eq 1}checked="checked"{/if} class="file-policy"/>
                                            {$policy.name}
                                        </li>                       
                                    {/foreach}
                                </ul>
                            </div>
                        </form>
                    </div>
                {/foreach}
            </div>
        </fieldset>
    </div>
</div>
*}
<div class="button_container">
    {if $hostname != 'localhost' and $hostaname != '127.0.0.1'}
        <a class="button" href="?module=dlp&action=newFile">{$translations[$new_file]}</a>
    {else}
        <span>
            {$translations[$t_cant_upload_files]}
        </span>
    {/if}
</div>


