{ assign var="t_manage_policies" value="Manage Policies" }
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
{ assign var="t_save" value="Save" }
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


<h2><a href="#">{$software_version}</a></h2>


<div class="assistant-wrapper">
    <form id="dlp_form-assistant" method="post" action="?module=dlp&action=updatePolicy">
        <input type="hidden" name="idPolicy" value="{$policy._id}">

        <fieldset {if $step neq 0} style="display:none;" {/if}>
            <legend>{$translations[$t_description]}</legend> 
            <p>
                <span class="entitle_medium">{$translations[$t_policy]}:</span>
                <input type="text" readonly="readonly" class="text medium required" name="policy_name" id="policy_name" value="{$policy.name}">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_short_desc]}:</span>
                <input type="text" class="text medium required" name="policy_description" id="policy_desc" value="{$policy.description}">
            </p>
        </fieldset>

        {if $mode neq "cloud"}
            <fieldset {if $step neq 1} style="display:none;" {/if}>
                <legend>{$translations[$t_enviroment]}</legend>
                <p class="intro">
                    {$translations[$t_text_enviroment]}:
                </p>
                <p>
                    <input type="checkbox" id="enviroment" name="policy_enviroment[webfilter]" value="true" {if $policy.enviroment.webfilter} checked="checked" {/if} onclick="javascript: return false;" />
                    <span>{$translations[$t_web_filter]}</span> <br/>
                    <input type="checkbox" id="enviroment" name="policy_enviroment[endpoint]" value="true" {if $policy.enviroment.endpoint} checked="checked" {/if} onclick="javascript: return false;" />
                    <span>{$translations[$t_endpoint]}</span> <br/>
                    <input type="checkbox" id="enviroment" name="policy_enviroment[mail]" value="true" {if $policy.enviroment.mail} checked="checked" {/if} onclick="javascript: return false;" />
                    <span>{$translations[$t_mail]}</span> <br/>
                </p>
            </fieldset>
        {/if}


        <fieldset {if $step neq 2} style="display:none;" {/if}>
            <legend>{$translations[$t_information_protect]}</legend>
            <p class="intro">
                {$translations[$t_text_information_protect]}.
            </p>
            <p>
            <fieldset class="accordion">
                <legend>{$translations[$t_concepts]} / {$translations[$t_subconcepts]}</legend>
                {foreach from=$concepts_subconcepts key=id_concept item=concept_subconcept}
                    <p>
                        <label >
							<input class="checkconcept" type="checkbox" id="catcheck" name="policy_concepts[]" {if $concept_subconcept.concept.checked} checked="checked" {/if} value="{$concept_subconcept.concept._id}" />
							{$concept_subconcept.concept.concept}
							</label>
                    <ul>
                        {foreach from=$concept_subconcept.subconcepts item=subconcept }
                            <li class="col">
                                <p>
                                    <label>
									    <input class="{$id_concept}" type="checkbox" id="catcheck" name="policy_subconcepts[]" {if $concept_subconcept.concept.checked} checked="checked" disabled="disabled" {/if} {if $subconcept.checked} checked="checked" {/if} value="{$subconcept._id}" />
										{$subconcept.description}
										</label>
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
								<input type="checkbox" id="catcheck" name="policy_rules[]" {if $rule.checked} checked="checked" {/if} value="{$rule._id}" />
								{$rule.description}</label>
                        </p>
						</li>
                    {/foreach}
                </fieldset>
            {/if}

            {if $nbr_files neq 0}
                <fieldset class="accordion">
                    <legend >{$translations[$t_files]}</legend>
                    {foreach from=$files item=file }
                        <li class="col">
						<p>
                            <label>
								<input type="checkbox" id="catcheck" name="policy_files[]" {if $file.checked} checked="checked" {/if} value="{$file._id}" />
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
								<input type="checkbox" id="catcheck" name="policy_network_places[]" {if $network_place.checked} checked="checked" {/if} value="{$network_place._id}" />							
								{$network_place.description}
							</label>
                        </p>
						</li>
                    {/foreach}
                </fieldset>
            {/if}

            <fieldset class="accordion">
                <legend>{$translations[$t_applications]}</legend>
                {foreach from=$applications item=application }
                    <li class="col">
					<p>
                        <label>
                        <input type="checkbox" id="catcheck" name="policy_applications[]" {if $application.checked} checked="checked" {/if} value="{$application._id}" />
						{$application.description}
						</label>
                    </p>
					</li>
                {/foreach}
            </fieldset>
            </p>
        </fieldset>

        <div class="form-dlp">
            <!-- casandoval: ugly hack, don't remove this empty div -->
            <div></div>
            <fieldset {if $step neq 3} style="display:none;" {/if}>
                <legend>{$translations[$t_set_action]}</legend>
                <p class="intro">
                    {$translations[$t_text_set_action]}:
                </p>
                <div id="cuadro-ar">
                    <p>
                        <b>{$translations[$t_active_policy]}</b>
                        <span class="table-control">{$translations[$t_severity]}</span>
                        <span class="table-control">{$translations[$t_action]}</span>
                    </p>
                    {assign var="notification" value="false"}
                    {foreach from=$groups key=key item=group}
                        <p>
                            <input type="checkbox" id="catcheck" name="groups[]" {if $group.checked} checked="checked" {/if} value="{$group.name}">
                            <label>{$group.name}</label>
                            <select name="severities[{$group.name}]" class="group_severity">
                                <option value="low"{if $group.severity == "low"} selected="selected" {/if}> {$translations[$t_low]}</option>
                                <option value="medium" {if $group.severity == "medium"} selected="selected" {/if}> {$translations[$t_medium]}</option>
                                <option value="high" {if $group.severity == "high"} selected="selected" {/if}> {$translations[$t_high]}</option>
                            </select>
                            <select name="actions[{$group.name}]" class="group_action">
                                {if $group.action eq "alert"}
                                    {assign var="notification" value="true"}
                                {/if}
                                <option value="log" {if $group.action == "log"} selected="selected" {/if}>{$translations[$t_log]}</option>
                                <option value="block" {if $group.action == "block"} selected="selected" {/if}>{$translations[$t_block]}</option>
                                <option value="alert" {if $group.action == "alert"} selected="selected" {/if}>{$translations[$t_alert]}</option>
                            </select>

                        </p>
                    {/foreach}
                </div>
                
                <br/><br/>
                
                <div id="cuadro-ar">
                    {$translations[$t_severity_parameter_internally]}
                </div>
                
                <br/><br/>
                
                <div id="email_notification" {if $notification eq "false"}style="display:none;"{/if}>
                    <p style="font-weight: bold; color: #BA1B36;">{$translations[$t_notification_email]}</p>
                    <p>{$translations[$t_text_email]}.</p> 
                    <br />
                    <div >
                        <p>
                            <span class="entitle_medium">{$translations[$t_email]}:</span>
                            <input type="text" class="text medium" name="policy_email" id="policy_email" value="{$policy.email}">
                        </p>
                    </div>
                </div>
                <br/>
            </fieldset>
        </div>

        <input type="submit" class="button" value="{$translations[$t_save]}" />

    </form>

</div>


</fieldset>

