{ assign var="start_date" value="Start Date" }
{ assign var="end_date" value="End Date" }
{ assign var="show_all" value="Show All" }
{ assign var="show_per_group" value="Show per Group" }
{ assign var="show_per_user" value="Show per User" }
{ assign var="show_per_computer" value="Show per Computer" }
{ assign var="filter" value="Filter" }
{ assign var="tuser" value="User" }
{ assign var="tgroup" value="Group" }
{ assign var="range_from" value="Range From" }
{ assign var="back" value="Back" }
{ assign var="most_accessed_urls" value="More Accessed Urls" }
{ assign var="show_access_history_of_the_url" value="Show Access History of the Url" }
{ assign var="number_of_access" value="Number of Access" }
{ assign var="most_blocked_urls" value="More Blocked Urls" }
{ assign var="most_blocked_categories" value="More Blocked Categories" }
{ assign var="list_of_most_blocked_urls" value="List of More Blocked Urls" }
{ assign var="number_of_locks" value="Number of Locks" }

<SCRIPT LANGUAGE="JavaScript">

var most_accessed_urls = "{$translations[$most_accessed_urls]}";
var show_access_history_of_the_url = "{$translations[$show_access_history_of_the_url]}";
var number_of_access = "{$translations[$number_of_access]}";
var most_blocked_urls = "{$translations[$most_blocked_urls]}";
var most_blocked_categories = "{$translations[$most_blocked_categories]}";
var list_of_most_blocked_urls = "{$translations[$list_of_most_blocked_urls]}";
var number_of_locks = "{$translations[$number_of_locks]}";	

</SCRIPT>


        {literal}
        <script>
	$(function() {
		$( "#datepickerstart" ).datepicker();
		$( "#datepickerend" ).datepicker();
	});
	</script>
        {/literal}


<div id="controls">
 {$translations[$start_date]}: <input class="text medium required" size="7" type="text" id="datepickerstart" value={$param_start}> 
 {$translations[$end_date]}: <input class="text medium required" size="7" type="text" id="datepickerend" value={$param_end}>
 <select class="text medium required" id="filter">
  <option value="0">{$translations[$show_all]}</option>
  <option value="1" {if $param_group != ""} selected {/if}>{$translations[$show_per_group]}</option>
  <option value="2" {if $param_user != ""} selected {/if}>{$translations[$show_per_user]}</option>
  <option value="3" {if $param_ip != ""} selected {/if}>{$translations[$show_per_computer]}</option>
 </select>

 <a class="button red" onclick="filter()">{$translations[$filter]}</a>
 <br>
 <span  class="filter_by" style="display:none" id="filter_by_user">{$translations[$tuser]}: <input class="text medium required"  type="text" id="user_field" value={$param_user}></span>
 <span class="filter_by" style="display:none" id="filter_by_group">{$translations[$tgroup]}:  
  <select class="text medium required" id="group">
  {foreach from=$groups item=group}
  <option value="{$group.name}" {if $param_group == $group.name} selected {/if}>{$group.name} </option>
  {/foreach}
  </select>
 </span>
 <span class="filter_by" style="display:none" id="filter_by_ip">
<p>
        <span class="entitle_medium">{$translations[$range_from]} :</span>
        <input type="text" size="3" maxlength="3" name="group1a" id="r1_1" max="255" class="ipgroup text short required" value="{$param_ip[0]}" >.
        <input type="text" size="3" maxlength="3" name="group2a" id="r1_2" max="255" class="ipgroup text short required" value="{$param_ip[1]}">.
        <input type="text" size="3" maxlength="3" name="group3a" id="r1_3" max="255" class="ipgroup text short required" value="{$param_ip[2]}">.
        <input type="text" size="3" maxlength="3" name="group4a" id="r1_4" max="255" class="ipgroup text short required" value="{$param_ip[3]}">
      </p>
 </span>
 </div>

<hr>
<div style="width:540px" id="chart1">
</div>
<div id="chart2-wrapper" style="display:none;">
<div style="width:540px;" id="chart2">
</div>
<br>
<button class='button red' onclick="hideHistogramByUrl()">{$translations[$back]}</button>
</div>
<br>
<div style="width:540px" id="flex1">
</div>


{$msg}
