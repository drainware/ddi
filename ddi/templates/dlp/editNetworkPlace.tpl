{ assign var="t_manage_network_places" value="Manage Network Places" }
{ assign var="t_network_uri" value="Network URI" }
{ assign var="t_short_desc" value="Short Desc" }
{ assign var="t_cancel" value="Cancel" }
{ assign var="t_reset" value="Reset" }
{ assign var="t_save" value="Save" }
{ assign var="t_policies_optional" value="Policies (optional)" }

<h2><a href="#">{$software_version}</a></h2>
<fieldset>
    <legend>{$translations[$t_manage_network_places]}</legend>
    <div class="form-dlp" id="dlp_edit_network_place">
        <form action="" method="get" id="edit_network_place_form">
            <p>
                <input type="hidden" name="module" value="dlp">
                <input type="hidden" name="action" value="updateNetworkPlace">
                <input type="hidden" name="id" value="{$network_place._id}">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_network_uri]}:</span>
                <input type="text" class="text medium required" name="network_uri" value="{$network_place.network_uri}">
            </p>
            <p>
                <span class="entitle_medium">{$translations[$t_short_desc]}:</span>
                <input type="text" class="text medium required" name="description" readonly="readonly" value="{$network_place.description}">
            </p>
            {if $nbr_policies neq 0}
                <p>
                    <span class="entitle_medium">{$translations[$t_policies_optional]}:</span>
                </p>
                <div style="padding-left: 65px;" id="margen-reglas">
                    {foreach from=$policies key=id item=policy}
                        <p>
                            <input type="checkbox" name="policies[]" value="{$id}" {if $policy.checked eq 1}checked="checked"{/if}>
                            <span>{$policy.name}</span>
                        </p>
                    {/foreach}
                </div>
            {/if}
            <div>
                <input type="button" class="button mr_10" onclick="window.location.href='?module=dlp&action=showNetworkPlaces'" value="{$translations[$t_cancel]}"/>
                <input type="reset" class="button mr_10" value="{$translations[$t_reset]}"/>
                <input type="submit" class="button red" value="{$translations[$t_save]}" />
            </div>
        </form>
    </div>
</fieldset>
