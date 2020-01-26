{ assign var="create_new_group" value="Create new group" }
{ assign var="name" value="Name" }
{ assign var="description" value="Description" }
{ assign var="create" value="Create" }
{ assign var="manage_profiles" value="Manage profiles" }
{ assign var="remove" value="Remove" }
{ assign var="create_profiles" value="Create profiles" }
{ assign var="import_group" value="Import Group" }
{ assign var="write_id" value="Write Id" }
{ assign var="add" value="Add" }
{ assign var="groups_message_lock" value="Groups message lock" }
{ assign var="t_policies" value="Policies" }
{ assign var="t_text_set_action" value="Text Policy Set Action" }
{ assign var="t_active_policy" value="Active Policy" }
{ assign var="t_action" value="Action" }
{ assign var="t_severity" value="Severity" }
{ assign var="t_save" value="Save" }
{ assign var="t_block" value="block" }
{ assign var="t_alert" value="alert" }
{ assign var="t_log" value="log" }
{ assign var="t_high" value="high" }
{ assign var="t_medium" value="medium" }
{ assign var="t_low" value="low" }
{ assign var="t_path_validate_correctly" value="path validate correctly" }
{ assign var="t_set_path" value="Set path of" }
{ assign var="t_choose_severity_policy" value="choose severity policy" }
{ assign var="t_reached_imported_groups" value="reached imported groups" }

<script language="javaScript">
    var jt_remove = "{$translations[$remove]}";
    var jt_policies = "{$translations[$t_policies]}";	
    var jt_save = "{$translations[$t_save]}"
</script>

{if $ddi_mode eq "unique" }
    <div style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
        {$translations[$groups_message_lock]}
    </div>
{else}
    <fieldset id="profiles">
        <legend>{$translations[$manage_profiles]}</legend>
        <p id="nro_policies" style="display: none;">{$nbr_policies}</p>
        <br/>
        {foreach key=gid item=group from=$groups}
            <div class="element" id="gid-{$gid}">
                <div class="info">
                    <div class="element-name">
                        <span>{$group.name}</span>
                        {if $ddi_auth eq "ldap"}
                            {if $group.name neq "default"}
                                {if $group.path}
                                    <small id="gpath-{$gid}"><a class="set-path" href="#path-{$group.name}">{$group.path}</a></small>
                                {else}
                                    <small id="gpath-{$gid}"><a class="new-path" href="#path-{$group.name}">{$translations[$t_path_validate_correctly]}</a></small>
                                        
                                {/if}
                                <div style="display:none">
                                    <div id="path-{$group.name}" class="form-add-path" style="padding:10px; background:#fff;">
                                        <fieldset>
                                            <legend>{$translations[$t_set_path]} {$group.name}</legend>
                                            <br/>
                                            <div>
                                                <form method="post" action="#" id="form-add-path">
                                                    <p>
                                                        <img class="cancel_path_selection" src="images/cross.png" alt="cancel">
                                                        <input type="hidden" name="id" value="{$gid}" />
                                                        <input style="width:85%" type="text" name="path" value="{$group.path}" class="gpath_input" placeholder="Import LDAP Group" />
                                                        <input class="button red add-path" type="button" value={$translations[$t_save]} />
                                                    </p>
                                                </form>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            {/if}
                        {/if}
                    </div>
                </div>
                {if $nbr_policies eq 0}
                    <div class="controls">
                        {if $group.name neq "default"}
                            <a class="button red mr_10 remove-group" href="#" id="{$group._id}">{$translations[$remove]}</a>
                        {/if}
                    </div>
                {else}
                    <div class="controls">
                        <a id="policy_{$group.name}" class="button mr_10 inline cboxElement" href="#{$group.name}">{$translations[$t_policies]}</a>
                        {if $group.name neq "default"}
                            <a class="button red mr_10 remove-group" href="#" id="{$group._id}">{$translations[$remove]}</a>
                        {/if}
                    </div>
                    <div style="display:none">
                        <div id="{$group.name}" class="form-dlp" style="padding:10px; background:#fff;">
                            <form method="post" action="?module=group&action=updatePolicies">
                                <input type="hidden" name="group" value="{$group.name}">
                                <fieldset>
                                    <legend><span>{$group.name}</span> - {$translations[$t_policies]}</legend>
                                    <p class="intro">
                                        {$translations[$t_choose_severity_policy]}{*$translations[$t_text_set_action]*}:
                                    </p>
                                    <div style="margin:0px 10px;width:400px" id="ancho-gru">
                                        <p>
                                            <b>{$translations[$t_active_policy]}</b>
                                            <span class="table-control">{$translations[$t_severity]}</span>
                                            <span class="table-control">{$translations[$t_action]}</span>
                                        </p>
                                                
                                        {foreach key=policy_id item=policy_object from=$group.policies}
                                            <p> 
                                                <input type="checkbox" name="policies[]" {if $policy_object.checked} checked="checked" {/if} value="{$policy_id}">
                                                <label for="">{$policy_object.name}</label>
                                                <select name="severities[{$policy_id}]">
                                                    <option value="low"{if $policy_object.severity == "low"} selected="selected" {/if}> {$translations[$t_low]}</option>
                                                    <option value="medium" {if $policy_object.severity == "medium"} selected="selected" {/if}> {$translations[$t_medium]}</option>
                                                    <option value="high" {if $policy_object.severity == "high"} selected="selected" {/if}> {$translations[$t_high]}</option>
                                                </select>
                                                <select name="actions[{$policy_id}]">
                                                    <option value="log" {if $policy_object.action == "log"} selected="selected" {/if}>{$translations[$t_log]}</option>
                                                    <option value="block" {if $policy_object.action == "block"} selected="selected" {/if}>{$translations[$t_block]}</option>
                                                    <option value="alert" {if $policy_object.action == "alert"} selected="selected" {/if}>{$translations[$t_alert]}</option>
                                                </select>
                                                        
                                            </p>
                                        {/foreach}
                                    </div>
                                    <br/>
                                    <input class="button" type="submit" value="{$translations[$t_save]}" />
                                </fieldset>
                            </form>
                        </div>
                    </div>
                {/if}
            </div>
        {/foreach}
    </fieldset>

    {if $nbr_policies neq 0}
        <div id="generic_policies" style="display:none">
            <p class="intro">
                {$translations[$t_text_set_action]}:
            </p>
              <div style="margin:0px 20px;width:400px" id="ancho-gru">
                <p>
                    <b>{$translations[$t_active_policy]}</b>
                    <span class="table-control">{$translations[$t_severity]}</span>
                    <span class="table-control">{$translations[$t_action]}</span>
                </p>

                {foreach key=policy_id item=policy_object from=$policies}
                    <p> 
                        <input type="checkbox" name="policies[]" value="{$policy_id}">
                        <label for="">{$policy_object.name}</label>
                        <select name="severities[{$policy_id}]">
                            <option value="low" selected="selected" > {$translations[$t_low]}</option>
                            <option value="medium" > {$translations[$t_medium]}</option>
                            <option value="high" > {$translations[$t_high]}</option>
                        </select>
                        <select name="actions[{$policy_id}]">
                            <option value="log" selected="selected" >{$translations[$t_log]}</option>
                            <option value="block" >{$translations[$t_block]}</option>
                            <option value="alert" >{$translations[$t_alert]}</option>
                        </select>
                    </p>
                {/foreach}
            </div>
            <br/>
            <input class="button" type="submit" value="{$translations[$t_save]}" />
        </div>
    {/if}
    <br/>
    {if $nro_groups < $nro_max_groups}
        <fieldset>
            <legend>{$translations[$create_profiles]}</legend><br/>
            <div>
                <form action="" id="import_form">
                    <img id="cancel_group_selection" src="images/cross.png" alt="cancel">
                    {if $ddi_auth eq "ldap" }
                        <p>
                            <input style="width:95%" type="text" name="group" id="group_input" placeholder="Import LDAP Group">
                        </p>
                    {/if}
                    <p>
                        <input style="width:85%" type="text" name="groupname" id="groupname_input" placeholder="{$translations[$write_id]}">
                        <input class="button red" type="submit" id="add-group" onclick="return false;" value="{$translations[$add]}">
                    </p>
                </form>
            </div>
        </fieldset>
    {else}
        <div style="padding:10px;line-height:30px;text-align:center;padding-top:25px;font-size:17px;">
            {$translations[$t_reached_imported_groups]}
        </div>
    {/if}
{/if}
<span id="calc"></span>