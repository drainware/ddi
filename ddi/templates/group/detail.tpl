{ assign var="web_filter" value="Web Filter" }
{ assign var="manage_profiles" value="Manage profiles" }
{ assign var="remove" value="Remove" }
{ assign var="change" value="Change" }
{ assign var="t_group_name" value="Group name" }
{ assign var="t_ldap_path" value="LDAP Path" }

<br />

<div class="section_header">
    <form action="?module=group&action=remove" method="POST" id="groups" >
        <fieldset>
            <legend>{$translations[$manage_profiles]} {$group_name}</legend>
            <p>
                <span class="entitle" >{$translations[$t_group_name]}:</span><br />
                <input style="width:93%;margin-bottom:10px;" type="text" name="group"  id="groupname_input" value="{$group_name}" disabled /> 
                <input type="hidden" name="name" id="groupname_input_form" value="{$group_name}">
                <br />
            </p>
            <p>
                {if $auth eq "ldap" }
                    <span class="entitle">{$translations[$t_ldap_path]}:</span>
                <div style="position: relative;">
                    <!--<img id="cancel_group_selection" src="images/cross.png" alt="cancel" style="display:inline;margin-right:8px;" >-->
                    <input style="width:93%" type="text" name="group" id="group_input" placeholder="{$translations[$import_group]}" value="{$ldap_path}" readonly="readonly"> 
                    <input type="hidden" name="path" id="grouppath_input_form" value="{$ldap_path}" readonly="readonly">
                    </p>
                   <!--<input type="text" class="text required extra_large" name="group_path" size="30" id="group_path_input" value="{$ldap_path}" /> -->
                    </p>
                    <br />
                {/if}
                {if $group_name != "default"}
                    <a class="button red mr_10" id="remove-group"  >{$translations[$remove]}</a>
                {/if}
                <br />
                <br />
                <!--{include file="utils/conf_form.tpl" }-->
        </fieldset>
    </form>
</div>