{ assign var="t_anomalous_activity" value="Detected anomalous activity" }
{ assign var="t_detected_on" value="We have detected on" }
{ assign var="t_following_events" value="the following events:" }


{$translations[$t_anomalous_activity]}

{$translations[$t_detected_on]} {$date} {$translations[$t_following_events]}

{foreach from=$events item=event_id}
https://www.drainware.com/ddi/?module=reporter&action=consoleDlpEvent&event_id='{$event_id}'

{/foreach}
