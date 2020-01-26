{ assign var="t_anomalous_activity" value="Detected anomalous activity" }
{ assign var="t_detected_on" value="We have detected on" }
{ assign var="t_following_events" value="the following events:" }

<div >
<h2 style="margin: 0px;">{$translations[$t_anomalous_activity]}</h2><br />
<p style="line-height: 120%; margin: 0px;">{$translations[$t_detected_on]} {$date} {$translations[$t_following_events]}</p>
{foreach from=$events item=event_id}
<p style="line-height: 120%; margin: 0px;">https://www.drainware.com/ddi/?module=reporter&action=consoleDlpEvent&event_id={$event_id}</p>
{/foreach}
<div><br/>
