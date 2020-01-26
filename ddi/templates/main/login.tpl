{ assign var="t_login_1" value="Login Text 1" }
{ assign var="t_user" value="User" }
{ assign var="t_password" value="Password" }
{ assign var="t_access" value="Access" }
{ assign var="t_email" value="Email" }
{ assign var="t_forgot_password" value="Have you forgot your password?" }
{ assign var="t_languaje" value="Language" }

<center>

    <img src="/ddi/images/cloud_logo2.png">
    {if $msg}
    {/if}
</center>

<div id="login">
    <div id="login_head">
        <h2>{$translations[$t_login_1]}</h2>
    </div>
    <div id="login_content">
        <form method="post" action="?module=main&amp;action=login">
			<p><font size="2" color="red">{$msg}</font></p>
            <p>
                <label for="">{$translations[$t_email]}</label>
                <input class="text" type="text" value="" name="name" />
            </p>
            <p>
                <label for="">{$translations[$t_password]}</label>
                <input class="text" type="password" value="" name="passwd" />
            </p>
            <p>
				<label for="">{$translations[$t_languaje]}</label>
                <select name="language" id="language">
                    {foreach from=$languages item=elem}
                        <option {if $lang==$elem} selected {/if} value="{$elem}">{$elem}</option>
                    {/foreach}
                </select>
                <font size="2" color="red">
                    <a href="?module=cloud&action=recoveryPassword">{$translations[$t_forgot_password]}</a>
                </font>
            </p>
            <p>
                <input name="redirect_to" type="hidden" value="{$redirect_to}" />
                <input class="submit" type="submit" value="{$translations[$t_access]}">
            </p>
            <div>
                 
            </div>     
        </form>
		<img class="corner"src="images/logo_corner.png">
    </div>

</div>
