{ assign var="t_explorer" value="Explorer" }
{ assign var="t_machine" value="Machine" }
{ assign var="t_ip" value="IP" }
{ assign var="t_path" value="Path" }
{ assign var="t_optional_directory_path" value="optional directory path" }
{ assign var="t_explore" value="explore" }
{ assign var="t_help_box_remote_file_explorer" value="Help Box Remote File Explorer" }
{ assign var="t_file_explorer" value="File Explorer" }


<script language="javascript">
    var account_type = "{$account_type}";
</script>

<h2><a href="#">{$software_version}</a></h2>
<fieldset>

    <legend>{$translations[$t_explorer]}</legend>

    <div id="forensics_search_list">

        <form id="file_explorer_form" method="post" action="?module=forensics&action=remoteQuery">
            <p>
                <span class="entitle_short">{$translations[$t_machine]}</span>
                <input type="text" name="device" value="{$device}" id="file_explorer_machine" class="text medium required" style="width: 340px;">
            </p>                
            <p>
                <span class="entitle_short">{$translations[$t_ip]}</span>
                <input type="text" name="ip" value="{$ip}" id="file_explorer_ip" class="text medium required" style="width: 340px;">
            </p>
            <input type="hidden" name="id" value="" id="file_explorer_id" />
            <input type="hidden" name="channel" value="{$channel}" id="file_explorer_channel" />
            <input type="hidden" name="command" value="listUnits" id="file_explorer_command" />
            <p>
                <span class="entitle_short">{$translations[$t_path]}</span>
                <input type="text" name="args" value="" id="file_explorer_path" class="text medium" style="width: 340px;" placeholder="{$translations[$t_optional_directory_path]}"/> 
            </p>

            <input type="button" value="{$translations[$t_explore]}" id="file_explorer_button" class="button"/>
        </form>
    </div>

</fieldset>
<div class="help_box">{$translations[$t_help_box_remote_file_explorer]}</div>
<fieldset id="remote_file_explore_results" style= "display: none;">
    <legend>{$translations[$t_file_explorer]}</legend>
    <center>
        <img id="loading_search" src="images/loading_search.gif" style="padding-top: 20px;"/>
    </center>
    <div id="file_explorer_list">                     
    </div>
</fieldset>
