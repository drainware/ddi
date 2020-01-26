{ assign var="t_general_configuration" value="General Configuration" }
{ assign var="t_credentials" value="Credentials" }
{ assign var="t_admin_user" value="Admin User" }
{ assign var="t_password" value="Password" }
{ assign var="t_repeat_please" value="Repeat, please" }
{ assign var="t_save" value="Save" }
{ assign var="t_authentication" value="Authentication" }
{ assign var="t_type" value="Type" }
{ assign var="t_base" value="Base" }
{ assign var="t_version" value="Version" }
{ assign var="t_port" value="Port" }
{ assign var="t_password" value="Password" }
{ assign var="t_username_attr" value="Username Attr" }
{ assign var="t_recursive_groups" value="Recursive Groups" }
{ assign var="t_import_local_ldap" value="Import Local Group To LDAP" }
{ assign var="t_choose_groups_import" value="Choose the groups to import:" }
{ assign var="t_imported_groups_deleted" value="No imported groups will be deleted." }
{ assign var="t_test_connection" value="Test LDAP Connection" }
{ assign var="t_company_administrator" value="COMPANY-DOMAIN\Administrator" }
{ assign var="t_administrator_password" value="Administrator password" }
{ assign var="t_dc_company_local" value="dc=company, dc=local" }
{ assign var="t_sam_account_name" value="sAMAccountName" }


<div id="content_info">
    <h2><a href="#">{$software_version}</a></h2>
</div>

<form action="?module=main&action=saveUserAuth" method="POST" enctype="multipart/form-data" id="user_auth_config">
    <fieldset>
        <legend>{$translations[$t_authentication]}</legend>

        <p>
            <span class="entitle_medium">{$translations[$t_type]}</span>
            <span>
                {* Verify last check in all browsers *}
                <input type="radio" name="auth" value="local" checked="checked" id="user_auth_local" /> <label for="">Local</label>
                <input type="radio" name="auth" value="ldap" {if $user_auth == "ldap"} checked="checked" {/if} id="user_auth_ldap" /> <label for="">LDAP</label>
            </span>
        </p>
        <div id="ldap_config">
            <p>
                <span class="entitle_medium">SSL</span>
                <input type="checkbox" name="ldap[ssl]" value="true" {if $ldap_conf.ssl} checked="checked" {/if}>
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_version]}</span>
                <select name="ldap[version]" id="ldap_conf_version">
                    <option value="2" {if $ldap_conf.version eq 2} selected="selected" {/if}>2.0</option>
                    <option value="3" {if $ldap_conf.version eq 3} selected="selected" {/if}>3.0</option>
                </select>
            </p>
            <p>
                <span class="entitle_medium">Host</span>
                <input type="text" name="ldap[host]" value="{$ldap_conf.host}" placeholder="127.0.0.1" class="text medium required" id="ldap_conf_host">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_port]}</span>
                <input type="text" name="ldap[port]" value="{$ldap_conf.port}" placeholder="389" class="text medium required" id="ldap_conf_port">
            </p>
            <p>
                <span class="entitle_medium">DN</span>
                <input type="text" name="ldap[dn]" value="{$ldap_conf.dn}" placeholder="{$translations[$t_company_administrator]}" class="text medium required" id="ldap_conf_dn">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_password]}</span>
                <input type="password" name="ldap[password]" value="{$ldap_conf.password}" placeholder="{$translations[$t_administrator_password]}" class="text medium required" id="ldap_conf_password">
            </p>            
            <p>
                <span class="entitle_medium">{$translations[$t_base]}</span>
                <input type="text" name="ldap[base]" value="{$ldap_conf.base}" placeholder="{$translations[$t_dc_company_local]}" class="text medium required" id="ldap_conf_base">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_username_attr]}</span>
                <input type="text" name="ldap[username_attr]" value="{$ldap_conf.username_attr}" placeholder="{$translations[$t_sam_account_name]}" class="text medium required">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_recursive_groups]}</span>
                <input type="checkbox" name="ldap[recursive_groups]" value="true" {if $ldap_conf.recursive_groups} checked="checked" {/if}>
                <input id="test-conf" class="button" type="button" value="{$translations[$t_test_connection]}" />
            </p>
        </div>
    </fieldset>
    <input id="save-conf" class="button red" type="button" value="{$translations[$t_save]}" />
</form>

<div style="display:none">
    <div id="local-groups" style="padding:10px; background:#fff;">
        <form id="import-local-ldap" method="post" action="?module=group&action=importLocalToLDAP">
            <fieldset>
                <legend>{$translations[$t_import_local_ldap]}</legend>
                <p>
                    {$translations[$t_choose_groups_import]}
                </p>
                <div id="list-groups" style="margin:0px 10px;width:400px">
                    {foreach from=$local_groups key=gid item=group}
                        <p>
                            <input type="hidden" name="groups[]" value="{$gid}"/>
                            <input type="checkbox" name="local_groups[]" value="{$gid}"/>
                            <label for="">{$group.name}</label>
                        </p>
                    {/foreach}
                </div>
                <span>
                    {$translations[$t_imported_groups_deleted]}
                </span>
                <input id="import-submit" class="button" type="button" value="Import" />
            </fieldset>
        </form>
    </div>
</div>
