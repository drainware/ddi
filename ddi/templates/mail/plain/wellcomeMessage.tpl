{ assign var="t_wellcome_drainware" value="Welcome to Drainware"}
{ assign var="t_use_credentials_access" value="You can use these credentials to access:"}
{ assign var="t_user" value="User"}
{ assign var="t_password" value="Password"}
{ assign var="t_password_configured" value=": The password you configured previously"}
{ assign var="t_endpoint_installation" value="For endpoint installation:"}
{ assign var="t_license" value="License"}
{ assign var="t_activate_account_message" value="To activate your account"}


{$translations[$t_wellcome_drainware]}

{$translations[$t_use_credentials_access]}
- {$translations[$t_user]}: {$email}
- {$translations[$t_password]} {$translations[$t_password_configured]}

{$translations[$t_endpoint_installation]}
- {$translations[$t_license]}:{$license}

{$translations[$t_activate_account_message]}

http://www.drainware.com/ddi/?module=cloud&action=validateUser&id={$id}


