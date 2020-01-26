{ assign var="t_login_1" value="Login Text 1" }
{ assign var="t_user" value="User" }
{ assign var="t_password" value="Password" }
{ assign var="t_access" value="Access" }
{ assign var="t_email" value="email" }
{ assign var="t_password" value="password" }
{ assign var="t_password_again" value="password again" }



<center>

<img src="/ddi/images/logo_big.png">

</center>

<div id="login">
  <div id="login_head">
    <h2>{$translations[$t_login_1]}</h2>
  </div>
  <div id="login_content">
    <form method="post" action="?module=main&amp;action=register">
      <input type="hidden" name="register" value="true" />
      <input type="hidden" name="lic" value="{$lic}" />
      <p>
        <label for="">{$translations[$t_email]}:</label><br />
        <input class="text" type="text" value="" name="email" />
      </p>
      <p>
        <label for="">{$translations[$t_password]}:</label><br />
        <input class="text" type="password" value="" name="passwd" />
      </p>
      <p>   
        <label for="">{$translations[$t_password_again]}:</label><br />
        <input class="text" type="password" value="" name="passwd2" />
      </p>
      <p>
        <input class="submit" type="submit" value="Registrar">
      </p>
      <!--
      <div style="float:right;float: right;margin-top: -40px;margin-right: 18px;">
        <label for="">Language</label>
        <select name="language" id="language">
        {foreach from=$languages item=elem}
           <option {if $lang==$elem} selected {/if} value="{$elem}">{$elem}</option>
        {/foreach}
        </select> 
      </div>
      -->     
     </form>
  </div>

</div>


<!--
<div id="info">Introduzca su usuario y clave</div>
<div id="header_bottom">
<h2>{$msg}</h2>
<table cellspacing="20" cellpadding="0" width="90%">
<form method="post" action="?module=main&amp;action=login">
<tbody><tr>
<td>Usuario (administrador):</td><td><input value="" name="name"></td>
</tr><tr>
<td>Clave: </td><td><input type="password" value="" name="passwd"></td>
</tr><tr>
<td></td><td><input type="submit" value="Entrar"></td>
</form>
</tr>
</tbody></table>
</div>
<br>
<br>
-->
