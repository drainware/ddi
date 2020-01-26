{ assign var="t_error_ocurred" value="An error ocurred." }


{$translations[$t_error_ocurred]}<br />
{foreach from=$state.messages item=message}
<b>{$message}</b><br />
{/foreach}

<a href="/?{foreach from=$return_to key=var item=value}{$var}={$value}&{/foreach}" >Back</a>