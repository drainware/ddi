{ assign var="t_start_date" value="Start Date" }
{ assign var="t_end_date" value="End Date" }
{ assign var="t_choose" value="Choose One" }
{ assign var="t_find" value="Find"}
{ assign var="t_filter" value="filter"}
{ assign var="t_list_report" value="List Report"}
{ assign var="t_remove" value="remove"}
{ assign var="t_download" value="download"}

<h2><a href="#">{$software_version}</a></h2>

<fieldset>

    <legend>{$translations[$t_find]}</legend>

    <div id="forensics_search_reports">

        <form id="get_search_reports_form" method="post" action="?module=reporter&action=getListReport">
            <p>
                {$translations[$t_start_date]}: <input type="text" name="start" value="{$date.start}" id="datepickerstart" class="text " size="10"> 
                {$translations[$t_end_date]}: <input type="text" name="end" value="{$date.end}" id="datepickerend" class="text" size="10"> 
                <input id="search_reports_filter" class="button red" type="submit" value="{$translations[$t_filter]}" onclick="return false;"/>
            </p>
        </form>
        <form id="search_reports_form" method="GET" action="?">                    
            <p>
                <input type="hidden" name="module" value="reporter"/>
                <input type="hidden" name="action" value="getForensicsReport"/>
                <label for="reports">{$translations[$t_list_report]}: </label>
                <select name="report" id="reports" style="width:320px">
                </select>
            </p>
            <input id="search_reports" class="button" style="float:right; margin-right: 215px;" type="submit" value="{$translations[$t_download]}" />
            <input id="remove_reports" class="button red" type="button" value="{$translations[$t_remove]}" />
        </form>
    </div>

</fieldset>


{foreach from=$search_report key=search_report_key item=search_report_item}
    <fieldset id="results_search{$search_report_key}" class="results_search">
        <legend id="nro_results">Results of {$report_list.$search_report_key} ({$report_list_count.$search_report_key})</legend>
        <div id="results">
            {foreach from=$search_report_item item=results}
                <fieldset  class="results_accordion">
                    <legend >{$results.machine} - {$results.ip} </legend>

                    {foreach from=$results.resultset item=result}
                        <p>
                            {$result.name}
                        </p>
                    {/foreach}  
                </fieldset>

            {/foreach}                 
        </div>

    </fieldset>   
{/foreach}    

