{ assign var="t_advanced_queries" value="Advanced Queries" }
{ assign var="t_date" value="Date" }
{ assign var="t_start_date" value="Start Date" }
{ assign var="t_end_date" value="End Date" }
{ assign var="t_enviroment" value="Enviroment" }
{ assign var="t_whom" value="Whom" }
{ assign var="t_choose" value="Choose One" }
{ assign var="t_show_all" value="Show All" }
{ assign var="t_show_per_computer" value="Show per Computer" }
{ assign var="t_computer_ip" value="Computer IP" }
{ assign var="t_show_per_group" value="Show per Group" }
{ assign var="t_group" value="Group" }
{ assign var="t_show_all" value="Show All" }
{ assign var="t_show_per_user" value="Show per User" }
{ assign var="t_user" value="User" }
{ assign var="t_type" value="Type" }
{ assign var="t_choose" value="Choose One" }
{ assign var="t_show_all" value="Show All" }
{ assign var="t_policies" value="Policies" }
{ assign var="t_concepts" value="Concepts" }
{ assign var="t_subconcepts" value="Subconcepts" }
{ assign var="t_rules" value="Rules" }
{ assign var="t_files" value="Files" }
{ assign var="t_network_places" value="Network Places" }
{ assign var="t_applications" value="Applications" }
{ assign var="t_severity" value="Severity" }
{ assign var="t_filter" value="Filter" }
{ assign var="t_dlp_few_data_message" value="DLP Few Data Message" }
{ assign var="t_dlp_events" value="DLP Events" }
{ assign var="t_message_dlp_detail" value="Message DLP Detail" }
{ assign var="t_high" value="high" }
{ assign var="t_medium" value="medium" }
{ assign var="t_low" value="low" }
{ assign var="t_limit" value="Limit" }
{ assign var="t_max_number_events" value="Max number os events" }
{ assign var="t_generate_report" value="Generate report" }
{ assign var="t_show_all_events" value="show all events" }
{ assign var="t_endpoint" value="Endpoint" }
{ assign var="t_source" value="Source:" }
{ assign var="t_sink" value="Sink:" }
{ assign var="t_pipe" value="Pipe:" }
{ assign var="t_also_screenshot" value="* Also screenshots" }
{ assign var="t_network_devices" value="Network Device" }
{ assign var="t_printer" value="Printer" }
{ assign var="t_clipboard_image" value="Clipboard Image*" }
{ assign var="t_clipboard_text" value="Clipboard Text" }
{ assign var="t_multiple" value="Multiple" }

{literal}
<style>
	fieldset:first-child{
		display:none;
	}
</style>
{/literal}

<script language="javascript">
    var t_dlp_events = "{$translations[$t_dlp_events]}";
    var t_message_dlp_event_detail = "{$translations[$t_message_dlp_detail]}";
</script>


{literal}
    <script>
        $(function() {
            $( "#datepickerstart" ).datepicker();
            $( "#datepickerend" ).datepicker();
        });
    </script>
{/literal}

<div>



    <fieldset>
        <legend id="advance_query">{$translations[$t_advanced_queries]}</legend>

        <form id="form-dlp-stats" method="GET" action="/ddi/">
            <input type="hidden" value="reporter" name="module" />
            <input type="hidden" value="showDlpStats" name="action" />
            <fieldset>
                <h2>{$translations[$t_date]}</h2>
                <p>
                    {$translations[$t_start_date]}: <input type="text" name="date[start]" value="{$fields.date.start}" id="datepickerstart" class="text" size="7"> 
                    {$translations[$t_end_date]}: <input type="text" name="date[end]" value="{$fields.date.end}" id="datepickerend" class="text" size="7"> 
                </p>
            </fieldset>

            <fieldset>
                <h2>{$translations[$t_enviroment]}</h2>
<!--
                <p> <input type="checkbox" name="origin[]" value="icap" {if $fields.origin.0 == "icap"} checked="checked" {/if} id="icap"> ICAP </p>
-->
                <p>
                    <input type="checkbox" name="origin[]" value="endpoint" {if $fields.origin.1 == "endpoint"} checked="checked" {/if} id="endpoint"> {$translations[$t_endpoint]}
                </p>                    
                <div id="endpoint_modules">
                    <div>
                        <p><b>{$translations[$t_source]}</b></p>
                        <div>
                            <input type="checkbox" name="endpoint_module[network_device_src]" value="network device src" {if $fields.endpoint_module.network_device_src == "network device src"} checked="checked" {/if} class="endpoint_module">
                            <label>{$translations[$t_network_devices]}</label>
                        </div>
                        <p></p>
                    </div>
                    <div>
                        <p><b>{$translations[$t_sink]}</b></p>
                        <!-- <div>
                            <input type="checkbox" name="endpoint_module[desktop]" value="desktop" {if $fields.endpoint_module.desktop == "desktop"} checked="checked" {/if} class="endpoint_module">
                            <label>Desktop</label>
                        </div> -->
                        <div>
                            <input type="checkbox" name="endpoint_module[dropbox]" value="dropbox" {if $fields.endpoint_module.dropbox == "dropbox"} checked="checked" {/if} class="endpoint_module">
                            <label>Dropbox</label>
                        </div>
                        <div>
                            <input type="checkbox" name="endpoint_module[google_drive]" value="google drive" {if $fields.endpoint_module.google_drive == "google drive"} checked="checked" {/if} class="endpoint_module">
                            <label>Google Drive</label>
                        </div>
                        <div>
                            <input type="checkbox" name="endpoint_module[network_device_dst]" value="network device dst" {if $fields.endpoint_module.network_device_dst == "network device dst"} checked="checked" {/if} class="endpoint_module">
                            <label>{$translations[$t_network_devices]}</label>
                        </div>
                        <div>
                            <input type="checkbox" name="endpoint_module[pendrive]" value="pendrive" {if $fields.endpoint_module.pendrive == "pendrive"} checked="checked" {/if} class="endpoint_module">
                            <label>Pendrive</label>
                        </div>
                        <div>
                            <input type="checkbox" name="endpoint_module[printer]" value="printer" {if $fields.endpoint_module.printer == "printer"} checked="checked" {/if} class="endpoint_module">
                            <label>{$translations[$t_printer]}</label>
                        </div>
                        <div>
                            <input type="checkbox" name="endpoint_module[skydrive]" value="skydrive" {if $fields.endpoint_module.skydrive == "skydrive"} checked="checked" {/if} class="endpoint_module">
                            <label>Skydrive</label>
                        </div>
                    </div>
                    <div>
                        <p><b>{$translations[$t_pipe]}</b></p>
                        <div>
                            <input type="checkbox" name="endpoint_module[clipboard_image]" value="clipboard image" {if $fields.endpoint_module.clipboard_image == "clipboard image"} checked="checked" {/if} class="endpoint_module">
                            <label>{$translations[$t_clipboard_image]}</label>
                        </div>
                        <div>
                            <input type="checkbox" name="endpoint_module[clipboard_text]" value="clipboard text" {if $fields.endpoint_module.clipboard_text == "clipboard text"} checked="checked" {/if} class="endpoint_module">
                            <label>{$translations[$t_clipboard_text]}</label>
                        </div>
                        <div>
                            <input type="checkbox" name="endpoint_module[keylogger]" value="keylogger" {if $fields.endpoint_module.keylogger == "keylogger"} checked="checked" {/if} class="endpoint_module">
                            <label>Keylogger</label>
                        </div>
                    </div>
			<p>{$translations[$t_also_screenshot]}</p>
                </div>
<!--
                <p> <input type="checkbox" name="origin[]" value="mail" {if $fields.origin.2 == "mail"} checked="checked" {/if} id="mail" disabled="disabled"> Mail </p>
-->
            </fieldset>
            <fieldset>
                <h2>{$translations[$t_whom]}</h2>
                <p>
                    {$translations[$t_choose]}: 
                    <select name="whom[value]" class="text medium required" id="whom">
                        <option value="0">{$translations[$t_show_all]}</option>
                        <option value="1" {if $fields.whom.value == "1"} selected="selected" {/if}>{$translations[$t_show_per_computer]}</option>
                        <option value="2" {if $fields.whom.value == "2"} selected="selected" {/if}>{$translations[$t_show_per_group]}</option>
                        <option value="3" {if $fields.whom.value == "3"} selected="selected" {/if}>{$translations[$t_show_per_user]}</option>
                    </select>
                </p>
                <span  class="whom_by" id="whom_by_user">
                    <p>
                        {$translations[$t_user]}: <input type="text" name="whom[user]" value="{$fields.whom.user}" id="user_field" class="text medium">
                    </p>
                </span>
                <span class="whom_by" style="display:none" id="whom_by_group">
                    <p>
                        {$translations[$t_group]}:  
                        <select name="whom[group][names][]" {if $fields.whom.group.multiple=="multiple"} multiple="multiple" {/if} id="group_field" class="text medium">
                            <option value="" class="group_field_opt">{$translations[$t_show_all]}</option>          
                            {foreach from=$groups item=group}
                                {assign var="selected" value="false"}
                                {foreach from=$fields.whom.group.names item=selected_group}
                                    {if $group.name == $selected_group}
                                        {assign var="selected" value="true"}
                                    {/if}
                                {/foreach}
                                <option value="{$group.name}" {if $selected=="true"} selected="selected" {/if} class="group_field_opt">{$group.name}</option>  
                            {/foreach}
                        </select>      
                    </p>
                    <p>
                        <input type="checkbox" name="whom[group][multiple]" value="multiple" {if $fields.whom.group.multiple == "multiple"} checked="checked" {/if} id="multiple_groups" /> 
                        <span>{$translations[$t_multiple]}</span>
                    </p>
                </span>
                <span class="whom_by" style="display:none" id="whom_by_ip">
                    <p>
                        <span class="text medium required">{$translations[$t_computer_ip]} :</span>
                        <input type="text" name="whom[ip][0]" value="{$fields.whom.ip.0}" size="3" maxlength="3" id="ip_field_1" max="255" class="ipgroup text short">.
                        <input type="text" name="whom[ip][1]" value="{$fields.whom.ip.1}" size="3" maxlength="3" id="ip_field_2" max="255" class="ipgroup text short">.
                        <input type="text" name="whom[ip][2]" value="{$fields.whom.ip.2}" size="3" maxlength="3" id="ip_field_3" max="255" class="ipgroup text short">.
                        <input type="text" name="whom[ip][3]" value="{$fields.whom.ip.3}" size="3" maxlength="3" id="ip_field_4" max="255" class="ipgroup text short">
                    </p>
                </span>
            </fieldset>

            <fieldset>
                <h2>{$translations[$t_type]}</h2>
                <p>
                    {$translations[$t_choose]}: 
                    <select name="type[value]" class="text medium required" id="type">
                        <option value="0">{$translations[$t_show_all]}</option>
                        <option value="1" {if $fields.type.value == "1"} selected="selected" {/if}>{$translations[$t_policies]}</option>
                        <option value="2" {if $fields.type.value == "2"} selected="selected" {/if}>{$translations[$t_concepts]}/{$translations[$t_subconcepts]}</option>
                        <option value="3" {if $fields.type.value == "3"} selected="selected" {/if}>{$translations[$t_rules]}</option>
                        <option value="4" {if $fields.type.value == "4"} selected="selected" {/if}>{$translations[$t_files]}</option>
                        <option value="5" {if $fields.type.value == "5"} selected="selected" {/if}>{$translations[$t_network_places]}</option>
                        {*<option value="6" {if $fields.type.value == "6"} selected="selected" {/if}>{$translations[$t_applications]}</option>*}
                    </select>
                </p>

                <span class="type_is" id="type_is_policy" style="display:none">
                    <p style="margin-left: 20px">
                        {$translations[$t_policies]}:
                        {foreach from=$policies item=policy}
                            {assign var="checked" value="false"}
                            {foreach from=$fields.policies item=cheched_policy}
                                {if $policy._id == $cheched_policy}
                                    {assign var="checked" value="true"}
                                {/if}
                            {/foreach}
                        <p style="margin-left: 40px">
                            <input type="checkbox" name="policies[]" value="{$policy._id}" {if $checked=="true"} checked="checked" {/if}/>
                            <label for="">{$policy.name}</label>
                        </p>
                    {/foreach}
                    </p>
                </span>  
                <span class="type_is" id="type_is_concept_subconcept" style="display:none">
                    <p style="margin-left: 20px">
                        {$translations[$t_concepts]}/{$translations[$t_subconcepts]}:
                        {foreach from=$concepts_subconcepts key=id_concept item=concept_subconcept}
                            {assign var="checked" value="false"}
                            {foreach from=$fields.concepts item=cheched_concept}
                                {if $concept_subconcept.concept._id == $cheched_concept}
                                    {assign var="checked" value="true"}
                                {/if}
                            {/foreach}   
                        <p style="margin-left: 30px;padding-left:20px">
                            <input type="checkbox" name="concepts[]" value="{$concept_subconcept.concept._id}" {if $checked=="true"} checked="checked" {/if} class="checkconcept"/>
                            <label for="" class="labelconcept" id="{$id_concept}">{$concept_subconcept.concept.concept}</label>
                        <ul>
                            {foreach from=$concept_subconcept.subconcepts item=subconcept }
                                {assign var="checked" value="false"}
                                {foreach from=$fields.subconcepts item=cheched_subconcept}
                                    {if $subconcept._id == $cheched_subconcept}
                                        {assign var="checked" value="true"}
                                    {/if}
                                {/foreach} 
                                <span class="label_{$id_concept}" style="display: none">
                                    <li>
                                        <p style="margin-left: 40px">
                                            <input type="checkbox" name="subconcepts[]" value="{$subconcept._id}" {if $checked=="true"} checked="checked" {/if} class="checksubconcept {$id_concept}"/>
                                            <label for="">{$subconcept.description}</label>
                                        </p>
                                    </li>
                                </span>
                            {/foreach}
                        </ul>
                        </p>
                    {/foreach}
                    </p>
                </span>
                <span class="type_is" id="type_is_rule" style="display:none">
                    <p style="margin-left: 20px">
                        {$translations[$t_rules]}:
                        {foreach from=$rules item=rule }
                            {assign var="checked" value="false"}
                            {foreach from=$fields.rules item=cheched_rule}
                                {if $rule._id == $cheched_rule}
                                    {assign var="checked" value="true"}
                                {/if}
                            {/foreach} 
                        <p style="margin-left: 40px">
                            <input type="checkbox" name="rules[]" value="{$rule._id}" {if $checked=="true"} checked="checked" {/if}/>
                            <label for="">{$rule.description}</label>
                        </p>
                    {/foreach}
                    </p>
                </span>

                <span class="type_is" id="type_is_file" style="display:none">
                    <p style="margin-left: 20px">
                        {$translations[$t_files]}:
                        {foreach from=$files item=file }
                            {assign var="checked" value="false"}
                            {foreach from=$fields.files item=cheched_file}
                                {if $file._id == $cheched_file}
                                    {assign var="checked" value="true"}
                                {/if}
                            {/foreach} 
                        <p style="margin-left: 40px">
                            <input type="checkbox" name="files[]" value="{$file._id}" {if $checked=="true"} checked="checked" {/if}/>
                            <label for="">{$file.name}</label>
                        </p>
                    {/foreach}
                    </p>
                </span>

                <span class="type_is" id="type_is_network_place" style="display:none">
                    <p style="margin-left: 20px">
                        {$translations[$t_network_places]}:
                        {foreach from=$network_places item=network_place }
                            {assign var="checked" value="false"}
                            {foreach from=$fields.network_places item=cheched_network_place}
                                {if $network_place._id == $cheched_network_place}
                                    {assign var="checked" value="true"}
                                {/if}
                            {/foreach} 
                        <p style="margin-left: 40px">
                            <input type="checkbox" name="network_places[]" value="{$network_place._id}" {if $checked=="true"} checked="checked" {/if}/>
                            <label for="">{$network_place.description}</label>
                        </p>
                    {/foreach}
                    </p>
                </span>
            </fieldset>
            
            <fieldset>
                <h2>{$translations[$t_applications]}</h2>
                <ul>
                    {foreach from=$applications item=application }
                        <li><p> <input type="checkbox" name="app[{$application}]" value="{$application}" {if $fields.app.$application==$application} checked="checked" {/if}> {$application} </p></li>
                    {/foreach}
                </ul>
            </fieldset>
                
            <fieldset>
                <h2>{$translations[$t_severity]}</h2>
                <ul>
                    <li><p> <input type="checkbox" name="severity[0]" value="high" {if $fields.severity.0=="high"} checked="checked" {/if} size="7" id="severity_high"> {$translations[$t_high]} </p></li>
                    <li><p> <input type="checkbox" name="severity[1]" value="medium" {if $fields.severity.1=="medium"} checked="checked" {/if} size="7"id="severity_medium"> {$translations[$t_medium]} </p></li>
                    <li><p> <input type="checkbox" name="severity[2]" value="low" {if $fields.severity.2=="low"} checked="checked" {/if} size="7" id="severity_low"> {$translations[$t_low]} </p></li>
                </ul>
            </fieldset>
            <fieldset>
                <h2>{$translations[$t_limit]}</h2>
                <p>
                    {$translations[$t_max_number_events]} <input type="text" name="limit" value="{$fields.limit}" id="events-limit" class="text" size="7"> 
                </p>
            </fieldset>
            <input type="hidden" value="{$query}" id="query" disabled="disabled"/>
            <input type="submit" value="{$translations[$t_filter]}" class="next button red"/>
        </form>
    </fieldset>

</div>

<div id="chart1-wrapper" >
    
    <div  id="chart1">
        <div style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
            {$translations[$t_dlp_few_data_message]}
        </div>
    </div>
    {if $events gt 1000}
        <p style="padding:10px;line-height:20px;text-align:center;padding-top:25px;font-size:14px;">
            {$translations[$t_show_all_events]}
        </p>
    {/if}
    <br/>            
    <div  id="chart2">
    </div>
</div>
<br>

<a class="prev button red" href="/ddi/?module=reporter&action=getDlpReport{$query}">{$translations[$t_generate_report]}</a>

{$msg}
