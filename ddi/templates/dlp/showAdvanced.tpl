{ assign var="advanced_dlp" value="Advanced DLP" }
{ assign var="block_encrypted_files" value="Block Encrypted Files" }
{ assign var="evidence_collector" value="Evidence Collector" }
{ assign var="text_evidence_collector" value="Text Evidence Collector" }
{ assign var="send_screenshot" value="Send Screenshot" }
{ assign var="save" value="Save" }
{ assign var="cancel" value="Cancel" }
{ assign var="t_help_box_advanced" value="Help Box Advanced" }
{ assign var="t_endpoint_modules" value="Endpoint Modules" }
{ assign var="t_which_modules_enabled" value="Which Modules Enabled" }
{ assign var="t_source" value="Source:" }
{ assign var="t_sink" value="Sink:" }
{ assign var="t_pipe" value="Pipe:" }
{ assign var="t_also_screenshot" value="* Also screenshots" }
{ assign var="t_none" value="none" }
{ assign var="t_high" value="high" }
{ assign var="t_medium" value="medium" }
{ assign var="t_low" value="low" }

<h2><a href="#">{$software_version}</a></h2>

<form action="" method="get">
    <input type="hidden" name="module" value="dlp">
    <input type="hidden" name="action" value="updateAdvanced">
    <fieldset>
        <legend>{$translations[$block_encrypted_files]}</legend>
        <div class="form-dlp">
            <div class="help_box">
	           {$translations[$t_help_box_advanced]}
            </div>
            <br/>
            {foreach from=$groups item=group}
                <div class="info">
                    <div class="element-name">
                        <input type="checkbox" id="catcheck" name="encrypted_files[]" value="{$group.name}" {if $group.encrypt eq 1 } checked="checked" {/if}>
                        <label>{$group.name}</label>
                        <input type="hidden" name="groups[]" value="{$group.name}">
                    </div>
                </div>
            {/foreach}
        </div>
    </fieldset>

    {if $user_type eq 'premium'}
        <fieldset>
            <legend>{$translations[$evidence_collector]}</legend>
            <div class="form-dlp">
                <p>{$translations[$text_evidence_collector]}.</p>
                </br>
                <label for="sc_severity">{$translations[$send_screenshot]}: </label>
                <select name="screenshot_severity" class="required" id="sc_severity">
                    <option {if $screenshot_severity eq "none" } selected {/if} value="none">{$translations[$t_none]}</option>
                    <option {if $screenshot_severity eq "low" } selected {/if} value="low">{$translations[$t_low]}</option>
                    <option {if $screenshot_severity eq "medium" } selected {/if} value="medium">{$translations[$t_medium]}</option>
                    <option {if $screenshot_severity eq "high" } selected {/if} value="high">{$translations[$t_high]}</option>
                </select>
            </div>
        </fieldset>
    {/if}

    <fieldset>
        <legend>{$translations[$t_endpoint_modules]}</legend>
        <div class="form-endpoint_modules">
            {$translations[$t_which_modules_enabled]}
            <div>
            
				<p><b>{$translations[$t_source]}</b></p>
                {foreach from=$endpoint_modules.source key=module item=module_object}
                    <div class="element-name">
                        <input type="checkbox" id="catcheck" name="endpoint_modules[]" value="{$module}" {if $module_object.check eq 1} checked="checked" {/if}>
                        <label>{$module_object.name}</label>
                    </div>
                {/foreach}
				</p>
			</div>
			<div>
                <p><b>{$translations[$t_sink]}</b></p>
                {foreach from=$endpoint_modules.sink key=module item=module_object}
                    <div class="element-name">
                        <input type="checkbox" id="catcheck" name="endpoint_modules[]" value="{$module}" {if $module_object.check eq 1} checked="checked" {/if}>
                        <label>{$module_object.name}</label>
                    </div>
                {/foreach}
			</div>
			<div>
                <p><b>{$translations[$t_pipe]}</b></p>
                {foreach from=$endpoint_modules.pipe key=module item=module_object}
                    <div class="element-name">
                        <input type="checkbox" id="catcheck" name="endpoint_modules[]" value="{$module}" {if $module_object.check eq 1} checked="checked" {/if}>
                        <label>{$module_object.name}</label>
                    </div>
                {/foreach}
            </div>
        </div>
    </fieldset>

   <p>{$translations[$t_also_screenshots]}</p>

    <div class="button_container">
        <input type="button" class="button mr_10" onclick="window.location.href='?module=dlp&action=showAdvanced'"value="{$translations[$cancel]}"/>
        <input type="submit" class="button red" value="{$translations[$save]}" />
    </div>

</form>
