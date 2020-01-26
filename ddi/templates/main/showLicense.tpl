{ assign var="t_general_configuration" value="General Configuration" }
{ assign var="t_license" value="License" }
{ assign var="t_registered" value="Registered" }
{ assign var="t_license_to" value="License To" }
{ assign var="t_modules" value="Modules" }
{ assign var="t_max_groups" value="Max Groups" }
{ assign var="t_expiry" value="Expiry" }
{ assign var="t_new_license" value="New License" }
{ assign var="t_yes" value="Yes" }
{ assign var="t_no" value="No" }
{ assign var="t_web_filter" value="Web Filter" }
{ assign var="t_save" value="Save" }
{ assign var="t_cant_upload_files" value="cant upload files" }

<div id="content_info">
    <h2><a href="#">{$software_version}</a></h2>
    <h3>{$translations[$t_general_configuration]}</h3>
</div>

<form action="?module=main&action=uploadLicense" method="POST" enctype="multipart/form-data" id="advanced_config">
    <fieldset>
        <legend>{$translations[$t_license]}</legend>
        <p>
            <span class="entitle_medium">{$translations[$t_registered]}</span>
            <span>
                <input type="radio" name="registered" checked="checked" disabled="disabled">
                {$translations[$t_yes]}
                <input type="radio" name="registered" {if $license.registered == "false"} checked="checked" {/if} disabled="disabled">
                {$translations[$t_no]}
            </span>
        </p>
        <p>
            <span class="entitle_medium">{$translations[$t_license_to]}</span>
            <input type="text" name="licensed" value="{$license.licensed_to}" readonly="readonly" class="text medium required">
        </p>
        <p>
            <span class="entitle_medium">{$translations[$t_modules]}</span>
            <span> 
                <input type="checkbox" name="module" {if $license.modules.webfilter} checked="checked" {/if} disabled="disabled">
                {$translations[$t_web_filter]}
                <input type="checkbox" name="module" {if $license.modules.dlp} checked="checked" {/if} disabled="disabled">
                DLP
                <input type="checkbox" name="module" {if $license.modules.atp} checked="checked" {/if} disabled="disabled">
                ATP
                <input type="checkbox" name="module" {if $license.modules.forensics} checked="checked" {/if} disabled="disabled">
                Forensics
            </span>
        </p>
        <p>
            <span class="entitle_medium">{$translations[$t_max_groups]}</span>
            <input type="text" name="max_groups" value="{$license.max_groups}" readonly="readonly" class="text medium required">
        </p>
        <p>
            <span class="entitle_medium">{$translations[$t_expiry]}</span>
            <input type="text" name="expiry" value="{$license.expiry}" readonly="readonly" class="text medium required">
        </p>
        <p>
            <span class="entitle_medium">{$translations[$t_new_license]}</span> 
            {if $hostname != 'localhost' and $hostaname != '127.0.0.1'}
                <input name="license_file" id="license_file" type="file" value="" style="width: 230px;">
            {else}
                <span>
                    {$translations[$t_cant_upload_files]}
                </span>
            {/if}
        </p>    
    </fieldset>
    <input class="button" type="submit" value="{$translations[$t_save]}" />
</form>