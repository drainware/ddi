{ assign var="password" value="Password" }
{ assign var="repeat_please" value="Repeat, please" }
{ assign var="general_configuration" value="General Configuration" }
{ assign var="advanced_config" value="Advanced Configuration" }
{ assign var="save" value="Save" }

<div id="content_info">
  <h2><a href="#">{$software_version}</a></h2>
  <h3>{$translations[$general_configuration]}</h3>
</div>

<div id="configuration" class="content_section">
    <form action="?module=main&action=editAdvanced" method="POST" enctype="multipart/form-data" id="advanced_config">
        <fieldset>
            <legend>{$translations[$advanced_config]}</legend>
            {$config_dev}
        </fieldset>
        <input class="button" type="submit" value="{$translations[$save]}" />
    </form>
</div>