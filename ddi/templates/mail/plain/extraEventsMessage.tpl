{ assign var="t_lucky_day" value="Today is your lucky day" }
{ assign var="t_hi_there" value="Hi there," }
{ assign var="t_thanks" value="Thanks to" }
{ assign var="t_extra_events" value=", you have 100 extra events free." }
{ assign var="t_free_inviting_contacts" value="Keep making more free events inviting your contacts" }
{ assign var="t_invite_contacts" value="Invite your contacts" }

{$translations[$t_lucky_day]}

{$translations[$t_hi_there]}

{$translations[$t_thanks]} {$email} {$translations[$t_extra_events]}

{$translations[$t_free_inviting_contacts]}

https://www.drainware.com/ddi/?module=main&action=showInviteFriend
