{ assign var="t_wellcome_not_registered" value="Welcome to Drainware you has not registered" }
{ assign var="t_organization_name" value="Organization name" }
{ assign var="t_phone_number" value="Phone number" }
{ assign var="t_address" value="Address" }
{ assign var="t_city" value="City" }
{ assign var="t_password" value="Password" }
{ assign var="t_repeat_password" value="Repeat Password" }
{ assign var="t_email" value="Email" }
{ assign var="t_state" value="State" }
{ assign var="t_country" value="Country" }
{ assign var="t_postal_code" value="Postal Code" }
{ assign var="t_installation_id" value="Installation ID" }
{ assign var="t_remember_later" value="Remember later" }


<h3>{$translations[$t_wellcome_not_registered]}</h3>

<br />

<form action="?module=register&action=submit&register=later" method="POST" id="">
<table border="0">
<tr><td>{$translations[$t_organization_name]}:</td><td><input name="organization_name" type="text" value="" /></td></tr>
<tr><td>{$translations[$t_email]}:</td><td><input name="email" type="text" value="" /></td></tr>
<tr><td>{$translations[$t_phone_number]}:</td><td><input name="phone" type="text" value="" /></td></tr>
<tr><td>{$translations[$t_address]}:</td><td><input name="address" type="text" value="" /></td></tr>
<tr><td>{$translations[$t_city]}:</td><td><input name="city" type="text" value="" /></td></tr>
<tr><td>{$translations[$t_state]}:</td><td><input name="state" type="text" value="" /></td></tr>
<tr><td>{$translations[$t_postal_code]}:</td><td><input name="zip" type="text" value="" /></td></tr>
<tr><td>{$translations[$t_country]}:</td><td><input name="country" type="text" value="" /></td></tr>
<!--

installation id:
- is generated automatically depending on organization and city
- check via ajax if the name is available

-->
<tr><td>{$translations[$t_installation_id]}:</td><td><input name="installation_id" type="text" value="" /></td></tr>
<tr><td>{$translations[$t_password]}:</td><td><input name="passwd" type="text" value="" /></td></tr>
<tr><td>{$translations[$t_repeat_password]}:</td><td><input name="passwd2" type="text" value="" /></td></tr>
<tr><td></td><td><input type="submit" name="submit" value="Register"></td></tr>
</table>
</form>
<div align="right">
<a href="?register=later">{$translations[$t_remember_later]}</a>
</div>