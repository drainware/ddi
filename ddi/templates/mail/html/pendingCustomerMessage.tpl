{ assign var="t_pending_customer" value="Pending Customer" }
{ assign var="t_email" value="Email" }
{ assign var="t_license" value="License" }
{ assign var="t_company" value="Company" }

<div>
<h2 style="margin: 0px;">{$translations[$t_pending_customer]}</h2>
<br />
<p style="line-height: 120%; margin: 0px;"><b>{$translations[$t_email]}</b>: {$email}</p>
<p style="line-height: 120%; margin: 0px;"><b>{$translations[$t_license]}</b>: {$license}</p>
<p style="line-height: 120%; margin: 0px;"><b>{$translations[$t_company]}</b>: {$company}</p>
</div>
<br/>
