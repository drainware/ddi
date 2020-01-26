{ assign var="t_allow_introduce" value="Allow us to introduce ourselves" }
{ assign var="t_hi_there" value="Hi there," }
{ assign var="t_wants_try" value="wants try Drainware" }
{ assign var="t_accept_invite" value="Accept invite" }

<div>
<h2 style="margin: 0px;">{$translations[$t_allow_introduce]}</h2>
<br />
<p style="line-height: 120%; margin: 0px;">{$translations[$t_hi_there]}</p>
<br />
<p style="line-height: 120%; margin: 0px;">{$email} {$translations[$t_wants_try]}</p>
<br />
<p style="line-height: 120%; margin: 0px;"><center><a style="font-size:16px;color:white;border-top: 1px #AB2222 solid;width:280px;border-left: 1px #9A1818 solid;font-weight:600;background-color: #E83333;border-radius:3px;border-right: 1px #9A1818 solid;border-bottom: 1px #8B0F0F solid;padding:14px 7px 14px 7px;max-width:280px;font-family:'Open Sans','lucida grande','Segoe UI',arial,verdana,'lucida sans unicode',tahoma,sans-serif;text-align:center;background-image: -webkit-gradient(linear,0% 0%,0% 100%,from(rgb(235, 55, 55)),to(rgb(207, 33, 33)));text-decoration:none;margin:0px auto 0px auto;display:block" href="{$referral_url}" target="_blank">{$translations[$t_accept_invite]}</a></center></p>
</div>
<br/>
