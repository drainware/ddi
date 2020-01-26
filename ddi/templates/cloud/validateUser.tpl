{ assign var="t_account_validated" value="Your account has been validated" }
{ assign var="t_redirect_wait" value="redirect wait seconds" }
{ assign var="t_cannot_activated" value="cannot activate user" }
{ assign var="t_account_already_activated" value="account already activated" }
{ assign var="t_now_can_login" value="Now you can login" }
{ assign var="t_here" value="here" }
{ assign var="t_problem_try_later" value="problem try later" }

<!--
<div align="center">
    <img src="images/robots/robot5.gif">
</div>
-->
<br/> <br/> <br/>

{if $resp eq 0 }
    <meta http-equiv="Refresh" content="3; URL=?module=main&action=login">
    <div class="cloud_validate_user">
        <p>{$translations[$t_account_validated]}</p>
    </div>
    <br/> <br/> <br/>
    <div class="cloud_validate_user_medium">
        <p>{$translations[$t_redirect_wait]}</p>
    </div>
{elseif $resp eq 1}
    <div class="cloud_validate_user">
        <p>{$translations[$t_cannot_activated]}</p>
    </div>
{elseif $resp eq 2}
    <meta http-equiv="Refresh" content="3; URL=?module=main&action=login">
    <div class="cloud_validate_user">
        <p>{$translations[$t_account_already_activated]}</p> 
    </div>
    <br/> <br/> <br/>
    <div class="cloud_validate_user_medium">
        <p>{$translations[$t_now_can_login]} <a href="?module=main&action=login" > {$translations[$t_here]} </a></p>
    </div>
{else}
    <div class="cloud_validate_user">
        <p>{$translations[$t_problem_try_later]}</p>
    </div>    
{/if}
