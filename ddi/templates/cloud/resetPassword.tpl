{ assign var="t_email" value="E-mail" }
{ assign var="t_new_password" value="New Password" }
{ assign var="t_confirm_password" value="Confirm Password" }
{ assign var="t_exceeded_time_password" value="exceeded time password" }
{ assign var="t_try_again" value="Try Again" }

<center>
    <img src="/ddi/images/logo_big.png">
</center>


<div id="login">
    <div id="login_content">
        <form action="?module=cloud&action=newPassword" method="POST" enctype="multipart/form-data">
            
            <p>
                <label for="">{$translations[$t_email]}:</label>
                 <input class="text required" type="hidden" name="id" value="{$id}" readonly="readonly" />
                <input class="text required" type="text" name="email" value="{$email}" readonly="readonly" />
            </p>
            {if $resp eq 1}
                <p>
                    <label for="">{$translations[$t_new_password]}:</label>
                    <input class="text required" type="password" name="new_passwd" value="" />
                </p>
                <p>
                    <label for="">{$translations[$t_confirm_password]}:</label>
                    <input class="text required" type="password" name="rep_passwd" value="" />
                </p>
                <p>
                    <input class="submit" type="submit" value="Send">
                </p>
            {elseif $resp eq 0} 
                <p>
                    <font size="2" color="red">{$translations[$t_exceeded_time_password]}</font>
                </p>
                <p>
                    <a class="submit" href="?module=cloud&action=recoveryPassword">{$translations[$t_try_again]}</a>
                </p>            
            {/if}
        </form>        
    </div>
</div>

