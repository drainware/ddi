{ assign var="new_file" value="New File" }
{ assign var="save" value="Save" }
{ assign var="reset" value="Reset" }
{ assign var="manage_files" value="Manage Files" }
{ assign var="new_file" value="New File" }
{ assign var="cancel" value="Cancel" }
 { assign var="t_browser_html5" value="browser html5" }

<h2><a href="#">{$software_version}</a></h2>
<h2>{$translations[$new_file]}</h2>
<hr>
<div class="form-dlp" id="dlp_new_file">
    <form action="" method="post" id="new_file_form">
        <div id="uploader">
            <p>{$translations[$t_browser_html5]}</p>
        </div>
    </form>
    <div>
        <input type="button" class="button mr_10" onclick="window.location.href='?module=dlp&action=showFiles'" value="{$translations[$cancel]}"/>
    </div>
</div>
    