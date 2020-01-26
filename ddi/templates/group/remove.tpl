{ assign var="next_users_depends" value="Next users depends on group" }
{ assign var="move_users_to" value="Move users to" }


{if $pending_users }

<form action="?module=group&action=remove&gid={$gid}">
{$translations[$next_users_depends]}:<br />
{foreach from=$pending_users item=user }
<b>{$user}</b><br />
{/foreach}

{$translations[$move_users_to]}:
<select name="{$user}">
{foreach from=$groups key=value item=this_group}
{if $value neq $gid }
<option value="{$value}">{$this_group->getConfValue('groupname')}</option>
{/if}
{/foreach}
</select>
<br /><br />
<input type="submit" value="Delete">
<input type="submit" value="Cancel">

</form>
{else}
{ if $state.error neq "true" }
{include file="utils/ok.tpl" }
{else}
{include file="utils/error.tpl" }
{ /if }

{/if}


