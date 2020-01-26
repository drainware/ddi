{ assign var="t_advanced_queries" value="Advanced Queries" }
{ assign var="t_date" value="Date" }
{ assign var="t_start_date" value="Start Date" }
{ assign var="t_end_date" value="End Date" }
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
{ assign var="t_applications" value="Applications" }
{ assign var="t_show_all" value="Show All" }
{ assign var="t_filter" value="Filter" }
{ assign var="t_atp_few_data_message" value="ATP Few Data Message" }
{ assign var="t_atp_events" value="ATP Events" }
{ assign var="t_message_atp_detail" value="Message ATP Detail" }
{ assign var="t_multiple" value="Multiple" }
{ assign var="t_show_reporter_atp" value="show all events reporter atp" }

{literal}
<style>
	fieldset:first-child{
		display:none;
	}
</style>
{/literal}

<SCRIPT LANGUAGE="JavaScript">

    var atp_events = "{$translations[$t_atp_events]}";	

</SCRIPT>


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
            <input type="hidden" value="showATPStats" name="action" />
            <fieldset>
                <h2>{$translations[$t_date]}</h2>
                <p>
                    {$translations[$t_start_date]}: <input type="text" name="date[start]" value="{$fields.date.start}" id="datepickerstart" class="text" size="7"> 
                    {$translations[$t_end_date]}: <input type="text" name="date[end]" value="{$fields.date.end}" id="datepickerend" class="text" size="7"> 
                </p>
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
                <span  class="whom_by" style="display:none" id="whom_by_user">
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
                    <p>
                        <input type="checkbox" name="whom[group][multiple]" value="multiple" {if $fields.whom.group.multiple == "multiple"} checked="checked" {/if} id="multiple_groups"> Multiple
                    </p>
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

            <fieldset >
                <h2>{$translations[$t_applications]}</h2>
                <p>
                    {$translations[$t_choose]}:
                    <select name="app[names][]" {if $fields.app.multiple=="multiple"} multiple="multiple" {/if} id="app_field" class="text medium">
                        <option value="" class="group_field_opt">{$translations[$t_show_all]}</option>          
                        {foreach from=$apps item=i_app}
                            {assign var="selected" value="false"}
                            {foreach from=$fields.app.names item=selected_app}
                                {if $i_app.name == $selected_app}
                                    {assign var="selected" value="true"}
                                {/if}
                            {/foreach}
                            <option value="{$i_app.name}" {if $selected=="true"} selected="selected" {/if} class="app_field_opt">{$i_app.name}</option>  
                        {/foreach}
                    </select>
                </p>
                <p>
                    <input type="checkbox" name="app[multiple]" value="multiple" {if $fields.app.multiple == "multiple"} checked="checked" {/if} id="multiple_apps"> {$translations[$t_multiple]}
                </p>
            </fieldset>
            <input type="hidden" value="{$query}" id="query" disabled="disabled"/>
            <p> <input type="submit" value="{$translations[$t_filter]}" class="next button red"/> </p>
        </form>
    </fieldset>

</div>


<div id="chart1-wrapper" >
    <div id="atp_histogram">
        <div style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
            {$translations[$t_atp_few_data_message]}
        </div>
    </div>
    {if $events gt 1000}
        <p style="padding:10px;line-height:20px;text-align:center;padding-top:25px;font-size:14px;">
            {$translations[$t_show_reporter_atp]}
        </p>
    {/if}
    <br/>

    {*$translations[$t_message_atp_detail]*}
    <div style="width:540px;" id="atp_console">
    </div>
</div>
<br>

{$msg}
