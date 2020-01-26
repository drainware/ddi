{ assign var="create_new_user" value="Create new user" }
{ assign var="create" value="Create" }
{ assign var="name" value="Name" }
{ assign var="t_group" value="Group" }
{ assign var="password" value="Password" }
{ assign var="repeat_please" value="Repeat, please" }
{ assign var="modify_user" value="Modify user" }
{ assign var="change" value="Change" }
{ assign var="remove" value="Remove" }
{ assign var="are_you_sure_you_want_to_remove" value="Are you sure you want to remove" }
{ assign var="users_message_lock" value="Users message lock" }
{ assign var="ldap_message_lock" value="LDAP message lock" }
{ assign var="t_install_endpoint_soon" value="Install endpoint as soon" }
{ assign var="t_user" value="User" }
{ assign var="t_group" value="Group" }
{ assign var="t_sure_endpoint_installed" value="sure endpoint installed" }

{if $ddi_mode eq "unique" }
    <div style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
        {$translations[$users_message_lock]}
    </div>
{else}
    <fieldset>
        <legend>{$translations[$modify_user]}</legend>

        {if $nro_users eq 0}
            {$translations[$t_install_endpoint_soon]}
        {else}
            <p>{$translations[$t_sure_endpoint_installed]}</p>
            </br>

            <p>
                <span class="entitle_medium">{$translations[$t_user]}</span>
                <span class="entitle_medium">{$translations[$t_group]}</span>
            </p>

            {foreach from=$users key=uid item=user}
                <form action="?module=user&action=modify" method="post" >
                    <p>
                        <span class="entitle_medium">{$user.name}</span>
                        <select id="{$uid}" name="{$uid}" class="modify-user {$auth}" multiple="multiple">
                            {foreach from=$user.groups key=gid item=group}
                                <option value="{$gid}" {if $group.checked }selected="selected"{/if}>{$group.name}</option>
                            {/foreach}
                        </select>
                        <a href="?module=user&action=remove&id={$uid}" class="combos3 button red" >{$translations[$remove]}</a>
                    </p>
                </form>    
            {/foreach}
        {/if}
    </fieldset>
{/if}