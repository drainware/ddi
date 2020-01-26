{ assign var="t_policy_creation_process" value="Policy Creation Process" }
{ assign var="t_step" value="Step" }
{ assign var="t_description" value="Description" }
{ assign var="t_policy" value="Policy" }
{ assign var="t_short_desc" value="Short Desc" }
{ assign var="t_enviroment" value="Enviroment" }
{ assign var="t_text_enviroment" value="Text Policy Enviroment" }
{ assign var="t_information_protect" value="Information to Protect" }
{ assign var="t_text_information_protect" value="Text Policy Information to Protect" }
{ assign var="t_concepts" value="Concepts" }
{ assign var="t_subconcepts" value="Subconcepts" }
{ assign var="t_rules" value="Rules" }
{ assign var="t_files" value="Files" }
{ assign var="t_network_places" value="Network Places" }
{ assign var="t_set_action" value="Set Action" }
{ assign var="t_text_set_action" value="Text Policy Set Action" }
{ assign var="t_active_policy" value="Active Policy" }
{ assign var="t_action" value="Action" }
{ assign var="t_severity" value="Severity" }
{ assign var="t_text_email" value="Text Policy Email" }
{ assign var="t_email" value="Email" }
{ assign var="t_finalize" value="Finalize" }
{ assign var="t_text_finalize" value="Text Policy Finalize" }
{ assign var="t_cancel" value="Cancel" }
{ assign var="t_next_step" value="Next" }
{ assign var="t_previous_step" value="Previous" }
{ assign var="t_block" value="block" }
{ assign var="t_alert" value="alert" }
{ assign var="t_log" value="log" }
{ assign var="t_high" value="high" }
{ assign var="t_medium" value="medium" }
{ assign var="t_low" value="low" }
{ assign var="t_applications" value="Applications" }
{ assign var="t_severity_parameter_internally" value="severity parameter internally" }
{ assign var="t_web_filter" value="Web Filter" }
{ assign var="t_endpoint" value="Endpoint" }
{ assign var="t_mail" value="Mail" }
{ assign var="t_notification_email" value="Notification Email" }

<script LANGUAGE="JavaScript">

    var wizard_step = "{$translations[$t_step]}";
    var wizard_next = "{$translations[$t_next_step]}";
    var wizard_previous = "{$translations[$t_previous_step]}";
    var wizard_cancel = "{$translations[$t_cancel]}";

</script>

<h2>{$translations[$t_policy_creation_process]}</h2>

<div class="assistant-wrapper">
    <form id="form-assistant" class="wizard_form" method="post" action="?module=dlp&action=createPolicy">
        <fieldset class="dwstep">
            <legend>{$translations[$t_description]}</legend> 
            <p>
                <span class="entitle_medium">{$translations[$t_policy]}:</span>
                <input type="text" class="text medium required" name="policy_name" id="policy_name" value="">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_short_desc]}:</span>
                <input type="text" class="text medium required" name="policy_description" id="policy_desc" value="">
            </p>
        </fieldset>

        {if $mode neq "cloud"}
            <fieldset class="dwstep">
                <legend>{$translations[$t_enviroment]}</legend>
                <p class="intro">
                    {$translations[$t_text_enviroment]}:
                </p>
                <p>
                    <input type="checkbox" id="enviroment" name="policy_enviroment[webfilter]" value="true" checked="checked" checked onclick="javascript: return false;" />
                    <span>{$translations[$t_web_filter]}</span> <br/>
                    <input type="checkbox" id="enviroment" name="policy_enviroment[endpoint]" value="true" checked="checked" onclick="javascript: return false;" />
                    <span>{$translations[$t_endpoint]}</span> <br/>
                    <input type="checkbox" id="enviroment" name="policy_enviroment[mail]" value="true" checked="checked" onclick="javascript: return false;" />
                    <span>{$translations[$t_mail]}</span> <br/>
                </p>
            </fieldset>
        {/if}
        <fieldset class="dwstep">
            <legend>{$translations[$t_information_protect]}</legend>
            <p class="intro">
                {$translations[$t_text_information_protect]}.
            </p>
            <p>
            <fieldset class="accordion">
                <legend >{$translations[$t_concepts]} / {$translations[$t_subconcepts]}</legend>
                {foreach from=$concepts_subconcepts key=id_concept item=concept_subconcept}
                    <p>
                        <label><input class="checkconcept" type="checkbox" id="catcheck" name="policy_concepts[]" value="{$concept_subconcept.concept._id}" />
                            {$concept_subconcept.concept.concept}</label>
                    <ul>
                        {foreach from=$concept_subconcept.subconcepts item=subconcept }
                            <li class="col">
                                <p>
                                    <label><input class="{$id_concept}" type="checkbox" id="catcheck" name="policy_subconcepts[]" value="{$subconcept._id}" />
                                        {$subconcept.description}</label>
                                </p>
                            </li>
                        {/foreach}
                    </ul>
                    </p>
                {/foreach}
            </fieldset>

            {if $nbr_rules neq 0}
                <fieldset class="accordion">
                    <legend>{$translations[$t_rules]}</legend>
                    {foreach from=$rules item=rule }
                        <li class="col">
                            <p>
                                <label>
                                    <input type="checkbox" id="catcheck" name="policy_rules[]" value="{$rule._id}" />
                                    {$rule.description}
                                </label>
                            </p>
                        </li>
                    {/foreach}
                </fieldset>
            {/if}

            {if $nbr_files neq 0}
                <fieldset class="accordion">
                    <legend>{$translations[$t_files]}</legend>
                    {foreach from=$files item=file }
                        <li class="col">
                            <p>
                                <label>
                                    <input type="checkbox" id="catcheck" name="policy_files[]" value="{$file._id}" />
                                    {$file.name}
                                </label>
                            </p>
                        </li>
                    {/foreach}
                </fieldset>
            {/if}

            {if $nbr_network_places neq 0}
                <fieldset class="accordion">
                    <legend>{$translations[$t_network_places]}</legend>
                    {foreach from=$network_places item=network_place }
                        <li class="col">
                            <p>
                                <label>
                                    <input type="checkbox" id="catcheck" name="policy_network_places[]" value="{$network_place._id}" />
                                    {$network_place.description}
                                </label>
                            </p>
                        </li>
                    {/foreach}
                </fieldset>
            {/if}

            <fieldset class="accordion">
                <legend>Applications</legend>
                {foreach from=$applications item=application }
                    <li class="col">
                        <p>
                            <label>
                                <input type="checkbox" id="catcheck" name="policy_applications[]" value="{$application._id}" />
                                {$application.description}
                            </label>
                        </p>
                    </li>
                {/foreach}
            </fieldset>
            </p>
        </fieldset>

        <div class="form-dlp" id="form-rules">
            <fieldset class="dwstep">
                <legend>{$translations[$t_set_action]}</legend>
                <p class="intro">
                    {$translations[$t_text_set_action]}:
                </p>
                <div style="margin-left:40px; margin-right:40px" id="margen-acciones">
                    <p>
                        <b>{$translations[$t_active_policy]}</b>
                        <span class="table-control">{$translations[$t_severity]}</span>
                        <span class="table-control">{$translations[$t_action]}</span>
                    </p>
                    {assign var="notification" value="false"}
                    {foreach from=$groups key=key item=group}
                        <p>
                            <input type="checkbox" id="catcheck" name="groups[]" value="{$group.name}">
                            <label for="">{$group.name}</label>
                            <select name="severities[{$group.name}]" class="group_action" id="severity">
                                <option value="low">{$translations[$t_low]}</option>
                                <option value="medium">{$translations[$t_medium]}</option>
                                <option value="high">{$translations[$t_high]}</option>
                            </select>
                            <select name="actions[{$group.name}]" class="group_action" id="action">
                                {if $group.action eq "alert"}
                                    {assign var="notification" value="true"}
                                {/if}
                                <option value="log">{$translations[$t_log]}</option>
                                <option value="block">{$translations[$t_block]}</option>
                                <option value="alert">{$translations[$t_alert]}</option>
                            </select>

                        </p>
                    {/foreach}
                </div>

                <br/><br/>

                <div class="help_box">
                    {$translations[$t_severity_parameter_internally]}
                </div>

                <br/><br/>

                <div id="email_notification" {if $notification eq "false"}style="display:none;"{/if}>
                    <p style="font-weight: bold; color: #BA1B36;">{$translations[$t_notification_email]}</p>
                    <p>{$translations[$t_text_email]}.</p> 
                    <br />
                    <div style="margin-left:40px; margin-right:40px">
                        <p>
                            <span class="entitle_medium">{$translations[$t_email]}:</span>
                            <input type="text" class="text medium" name="policy_email" id="policy_email" value="">
                        </p>
                    </div>
                </div>
                <br/>
            </fieldset>
        </div>

        <fieldset class="dwstep">
            <legend>{$translations[$t_finalize]}</legend>
            <p class="intro">
                {$translations[$t_text_finalize]}.
            </p>
            <input class="next button" onclick="trak.io.track('New Policy');return true;" type="submit" id="saveForm" value="{$translations[$t_finalize]}" />
        </fieldset>

    </form>

</div>
