{ assign var="t_lucky_day" value="Today is your lucky day" }
{ assign var="t_hi_there" value="Hi there," }
{ assign var="t_thanks" value="Thanks to" }
{ assign var="t_extra_events" value=", you have 100 extra events free." }
{ assign var="t_free_inviting_contacts" value="Keep making more free events inviting your contacts" }
{ assign var="t_invite_contacts" value="Invite your contacts" }

<div>
<h2 style="margin: 0px;">{$translations[$t_lucky_day]}</h2>
<br />
<p style="line-height: 120%; margin: 0px;">{$translations[$t_hi_there]}<p>
<br />
<p style="line-height: 120%; margin: 0px;">{$translations[$t_thanks]} {$email} {$translations[$t_extra_events]}</p>
<br />
<p style="line-height: 120%; margin: 0px;">{$translations[$t_free_inviting_contacts]}</p>
<br />
<p style="line-height: 120%; margin: 0px;"><a href="https://www.drainware.com/ddi/?module=main&action=showInviteFriend" target="_blank">{$translations[$t_invite_contacts]}</a></p>
</div>
<br/>
