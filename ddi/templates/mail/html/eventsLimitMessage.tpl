{ assign var="t_dude_fire" value="Dude, your network is on Fire!" }
{ assign var="t_reached_maximun" value="reached maximun limit message" }
{ assign var="t_upgrade_account" value="upgrade account limit message" }
{ assign var="t_change_premium" value="Change to premium" }

<div >
<h2 style="margin: 0px;">{$translations[$t_dude_fire]}</h2>
<br/>
<p style="line-height: 120%; margin: 0px;">{$translations[$t_reached_maximun]}</p> <br/>
<p style="line-height: 120%; margin: 0px;">
{$translations[$t_upgrade_account]} <a href="https://www.drainware.com/ddi/?module=main&action=showCloudConfig">{$translations[$t_change_premium]}</a>
</p>
</div>
<br/>
