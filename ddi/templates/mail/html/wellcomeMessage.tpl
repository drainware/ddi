{ assign var="t_wellcome_drainware" value="Welcome to Drainware" }
{ assign var="t_use_credentials_access" value="You can use these credentials to access:" }
{ assign var="t_user" value="User" }
{ assign var="t_password" value="Password" }
{ assign var="t_password_configured" value=": The password you configured previously" }
{ assign var="t_endpoint_installation" value="For endpoint installation:" }
{ assign var="t_license" value="License" }
{ assign var="t_activate_account_message" value="To activate your account" }


<div>
<h2 style="margin: 0px;">{$translations[$t_wellcome_drainware]}</h2>
<br />
<p style="line-height: 120%; margin: 0px;">{$translations[$t_use_credentials_access]}</p>
<br />
<p style="line-height: 120%; margin: 0px;"><b>{$translations[$t_user]}</b>: {$email}</p>
<p style="line-height: 120%; margin: 0px;"><b>{$translations[$t_password]}</b>{$translations[$t_password_configured]}</p>
<br />
<p style="line-height: 120%; margin: 0px;">{$translations[$t_endpoint_installation]}</p>
<br />
<p style="line-height: 120%; margin: 0px;"><b>{$translations[$t_license]}</b>: {$license}</p>
<br />
<p style="line-height: 120%; margin: 0px;">{$translations[$t_activate_account_message]}</p>
<a href="http://www.drainware.com/ddi/?module=cloud&action=validateUser&id={$id}">http://www.drainware.com/ddi/?module=cloud&action=validateUser</a>
</div>
<br/>
