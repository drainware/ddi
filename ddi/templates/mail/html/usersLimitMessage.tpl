{ assign var="t_the_license" value="The license" }
{ assign var="t_exceeded_users_number" value="exceeded number users message" }
{ assign var="t_new_user" value="New user:" }

<div >
                    <h2 style="margin: 0px;">{$translations[$t_the_license]} {$license} {$translations[$t_exceeded_users_number]}</h2>
                    <br/>
                    <p style="line-height: 120%; margin: 0px;">{$translations[$t_new_user]} {$user} </p> <br/>

</div>
<br/>
