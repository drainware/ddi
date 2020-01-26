{ assign var="t_password_reset_procedure" value="Password Reset Procedure" }
{ assign var="t_reestablishing_password" value="reestablishing password" }
{ assign var="t_click_following_link" value="click following link" }
{ assign var="t_copy_paste_url" value="copy paste URL" }
{ assign var="t_receive_message_error" value="receive message error" }

{$translations[$t_password_reset_procedure]}

{$translations[$t_reestablishing_password]} {$email} {$translations[$t_click_following_link]}

https://www.drainware.com/ddi/?module=cloud&action=resetPassword&id={$token}

{$translations[$t_copy_paste_url]}

{$translations[$t_receive_message_error]}
