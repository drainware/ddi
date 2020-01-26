{ assign var="t_general_configuration" value="General Configuration" }
{ assign var="t_credentials" value="Credentials" }
{ assign var="t_admin_user" value="Admin User" }
{ assign var="t_password" value="Password" }
{ assign var="t_repeat_please" value="Repeat, please" }
{ assign var="t_save" value="Save" }

{ assign var="t_old_password" value="Old Password" }
{ assign var="t_new_password" value="New Password" }
{ assign var="t_confirm_password" value="Confirm Password" }

<link rel="stylesheet" href="css/showCredentials.css" />

<div id="content_info">
  <h2><a href="#">{$software_version}</a></h2>
</div>

<form action="?module=main&action=changeCredentials" method="POST" enctype="multipart/form-data" id="advanced_config">
    <fieldset>
        <legend>{$translations[$t_credentials]}</legend>
        <p>
            <span class="entitle_medium">{$translations[$t_admin_user]}</span>
            <input type="text" name="user" value="{$user}" readonly="readonly" class="text medium required">
        </p>
        <p>
            <span class="entitle_medium">{$translations[$t_old_password]}{*$translations[$t_password]*}</span>
            <input type="password" name="old_passwd" class="text medium required">
        </p>
        <p>
            <span class="entitle_medium">{$translations[$t_new_password]}{*$translations[$t_password]*}</span>
            <input type="password" name="new_passwd" class="text medium required">
        </p>
        <p>
            <span class="entitle_medium">{$translations[$t_confirm_password]}{*$translations[$t_password]*} {*$translations[$t_repeat_please]*}</span>
            <input type="password" name="rep_passwd" value="" class="text medium required">
        </p>
    </fieldset>
    <input class="button red" type="submit" value="{$translations[$t_save]}" />

</form>