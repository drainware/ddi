{ assign var="t_manage_applications" value="Manage Applications" }
{ assign var="t_application_creation_process" value="Aplication Protect Creation Process" }
{ assign var="t_step" value="Step" }
{ assign var="t_description" value="Description" }
{ assign var="t_application" value="Application" }
{ assign var="t_short_desc" value="Short Desc" }
{ assign var="t_variables" value="Variables" }
{ assign var="t_text_variables" value="Text App Variables" }
{ assign var="t_extensions" value="Extensions" }
{ assign var="t_text_extensions" value="Text App Extensions" }
{ assign var="t_extension_s" value="Extension(s)" }
{ assign var="t_finalize" value="Finalize" }
{ assign var="t_text_finalize" value="Text App Finalize" }
{ assign var="t_cancel" value="Cancel" }
{ assign var="t_next_step" value="Next" }
{ assign var="t_previous_step" value="Previous" }
{ assign var="t_force_termination" value="Force Termination" }
{ assign var="t_resume_monitoring" value="Resume Monitoring" }
{ assign var="t_edit_variables" value="Edit Variables" }
{ assign var="t_other_extensions" value="Other extensions" }
{ assign var="t_yes" value="yes" }
{ assign var="t_no" value="no" }

<SCRIPT LANGUAGE="JavaScript">
    var wizard_step = "{$translations[$t_step]}";
    var wizard_next = "{$translations[$t_next_step]}";
    var wizard_previous = "{$translations[$t_previous_step]}";
    var wizard_cancel = "{$translations[$t_cancel]}";
</SCRIPT>

<h2>{$translations[$t_manage_applications]}</h2>
<div class="assistant-wrapper">
    <form id="form-assistant" class="wizard_form" method="post" action="?module=atp&action=updateApp">
        <input type="hidden" name="app_id" value="{$app._id}" />
        <fieldset class="dwstep">
            <legend>{$translations[$t_description]}</legend> 
            <p>
                <span class="entitle_medium">{$translations[$t_application]}:</span>
                <input type="text" class="text medium required" name="app_name" value="{$app.name}" {if $app.predefined_app} readonly="readonly" {/if}/>
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_short_desc]}:</span>
                <input type="text" class="text medium required" name="app_description" value="{$app.description}" readonly="readonly" />
            </p>
        </fieldset> 
        <fieldset class="dwstep">
            <legend>{$translations[$t_variables]}</legend> 
            <p>{$translations[$t_text_variables]}: </p><br/>
            
            {if $app.predefined_app}
                <p>
                    <span class="entitle_medium">{$translations[$t_edit_variables]}:</span>
                    <input type="checkbox" name="app_editable" value="1" {if $app.editable eq 1} checked="checked" {/if} id="app_editable"/>
                </p>
            {/if}
                
            {foreach from = $atp_variables key=k_variable item=i_variable}
                <p id="app_variables">
                    <span class="entitle_medium">{$i_variable}:</span>
                    <!--
                    <select name="app_vars[{$k_variable}][type]" style="width: 65px;">
                        <option value="dword" {if $app.variables.$k_variable.type == "dword"} selected="selected" {/if}>dword</option>
                        <option value="hex" {if $app.variables.$k_variable.type == "hex"} selected="selected" {/if}>hex</option>
                    </select>
                    -->
                    <input type="text" class="text medium" name="app_vars[{$k_variable}][value]" value="{$app.variables.$k_variable.value}" {if  $app.predefined_app}{if $app.editable eq 0} readonly="readonly" {/if}{/if} {if $k_variable eq "SearchString"} placeholder="Hex. ej: 90,C0,FF" {/if} />
                </p>
            {/foreach}
             <p>
                <span class="entitle_medium">{$translations[$t_force_termination]}:</span>
                <input type="radio" name="app_force_termination" value="1" {if $app.force_termination eq 1} checked="checked" {/if} />
                <label id="">yes</label>
                <input type="radio" name="app_force_termination" value="0" {if $app.force_termination eq 0} checked="checked" {/if} />
                <label id="">no</label>
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_resume_monitoring]}:</span>
                <select name="app_resume_monitoring" style="width: 65px;">
                     <option value="0" {if $app.resume_monitoring eq 0} selected="selected" {/if} >{$translations[$t_no]}</option>
                     <option value="1" {if $app.resume_monitoring eq 1} selected="selected" {/if}>{$translations[$t_yes]}</option>
                 </select>
            </p>
       </fieldset>
        <fieldset class="dwstep">
            <legend>{$translations[$t_extensions]}</legend>
            <p> {$translations[$t_text_extensions]} </p> <br/>
            {if not $app.predefined_app}
                <p>
                    <span class="entitle_medium">{$translations[$t_extension_s]}:</span>
                    <input type="text" class="text medium" name="app_extensions" value="{$app.extensions}" />
                </p>
            {else}
                <p>
                    <span class="entitle_medium">{$translations[$t_extension_s]}:</span>
                    <input type="text" class="text medium" value="{$app.extensions}" readonly="readonly" />
                </p>
                <p>
                    <span class="entitle_medium">{$translations[$t_other_extensions]}:</span>
                    <input type="text" class="text medium" name="app_extensions" value="{$app.other_extensions}" />
                </p>                
            {/if}

            
            
        </fieldset>   
        <fieldset class="dwstep">
            <legend>{$translations[$t_finalize]}</legend>
            <p class="intro">
                {$translations[$t_text_finalize]}
            </p>
            <input class="next button" type="submit" id="saveForm" value="{$translations[$t_finalize]}" />
        </fieldset>
    </form>
</div>
