{ assign var="t_general_configuration" value="General Configuration" }
{ assign var="t_credentials" value="Credentials" }
{ assign var="t_admin_user" value="Admin User" }
{ assign var="t_password" value="Password" }
{ assign var="t_repeat_please" value="Repeat, please" }
{ assign var="t_save" value="Save" }
{ assign var="t_reset_password_recovery" value="reset password recovery" }
{ assign var="t_email" value="E-mail" }

<center>
    <img src="/ddi/images/cloud_logo2.png">
</center>


<div id="login">
    <div id="login_content">
        <form action="?module=cloud&action=recoveryPassword" method="POST" enctype="multipart/form-data">
            <p>
                <font size="2" color="grey">{$translations[$t_reset_password_recovery]}.</font>
            </p>            
            <p>
                <label for="">{$translations[$t_email]}:</label>
                <input class="text required" type="text" name="email" value="" />
            </p>
            <p>
                <font size="2" color="red">{$msg}</font>
            </p>
            <p>
                <input class="submit" type="submit" value="Send Email">
            </p>
        </form>        
    </div>
</div>

