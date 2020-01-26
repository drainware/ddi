{ assign var="manage_rules" value="Manage Rules" }
{ assign var="dlp_rules" value="Rules" }
{ assign var="t_search_list" value="Search by list" }
{ assign var="t_search_name" value="Search name" }
{ assign var="t_search_file" value="Search file" }
{ assign var="t_search_button" value="Search button" }
{ assign var="t_help_box_search_remote_multiple" value="Help Box Search Remote Multiple" }

<h2><a href="#">{$software_version}</a></h2>
<fieldset>

    <legend>{$translations[$t_search_list]}</legend>

    <div id="forensics_search_list">

        <form id="search_list_form" method="post" action="?module=forensics&action=remoteQueryList" enctype="multipart/form-data">
            <p>
                <span class="entitle_short">{$translations[$t_search_name]}</span>
                <input type="text" name="name" value="" id="search_list_name" class="text medium required">
            </p>                
            <p>
                {if $hostname != 'localhost' and $hostaname != '127.0.0.1'}
                    <span class="entitle_short">{$translations[$t_search_file]}</span>
                    <input type="file" name="list" value="" id="search_list_file" style="width: 350px;">
                {/if}
            </p>  
            <input id="search_list" class="button" type="submit" value="{$translations[$t_search_button]}" />
        </form>
    </div>

</fieldset>

<div class="help_box">
    {$translations[$t_help_box_search_remote_multiple]}
</div>
