{ assign var="t_manage_rules" value="Manage Rules" }
{ assign var="t_rule" value="Rule" }
{ assign var="t_short_desc" value="Short Desc" }
{ assign var="t_cancel" value="Cancel" }
{ assign var="t_reset" value="Reset" }
{ assign var="t_save" value="Save" }
{ assign var="t_verification_code_optional" value="Verification code (optional)" }
{ assign var="t_policies_optional" value="Policies (optional)" }
{ assign var="t_enter_keyword_expression" value="enter keyword expression" }

<h2><a href="#">{$software_version}</a></h2>
<fieldset>
    <legend>{$translations[$t_manage_rules]}</legend>
    <div class="form-dlp" id="dlp_new_rule">
        <form action="" method="get" id="new_rule_form">
            <p>
                <input type="hidden" name="module" value="dlp">
                <input type="hidden" name="action" value="createRule">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_rule]}:</span>
                <input type="text" class="text medium required" name="rule" value="" placeholder="{$translations[$t_enter_keyword_expression]}">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_short_desc]}:</span>
                <input type="text" class="text medium required" name="description" value="">
            </p>
            <p>
                <span class="entitle_medium" style="vertical-align:top !important;">{$translations[$t_verification_code_optional]}:</span>
                <span>
                    <br/><br/>&lt;?php<br/>
                    function verify($match){literal}{{/literal}<br/>
                    <textarea id="new_verify" name="verify" cols="80" rows="15" placeholder="//optional"></textarea>
                    <br/> return $return_val; <br/> {literal}}{/literal}
                    <br/>?&gt;
                </span>
            </p>
            {if $nbr_policies neq 0}
                <p>
                    <span class="entitle_medium">{$translations[$t_policies_optional]}:</span>
                </p>
                <div style="padding-left: 65px;"  id="margen-reglas">
                    {foreach from=$policies key=id item=policy}
                        <p>
                            <input type="checkbox" name="policies[]" value="{$id}" {if $policy.checked eq 1}checked="checked"{/if}>
                            <span>{$policy.name}</span>
                        </p>
                    {/foreach}
                </div>
            {/if}
            <div>
                <input type="button" class="button mr_10" onclick="window.location.href='?module=dlp&action=showRules'" value="{$translations[$t_cancel]}"/>
                <input type="reset" class="button mr_10" value="{$translations[$t_reset]}"/>
                <input id="save-nform" type="button" class="button red" value="{$translations[$t_save]}" />
            </div>
        </form>
    </div>
</fieldset>

