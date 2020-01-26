{ assign var="t_manage_network_places" value="Manage Network Places" }
{ assign var="t_network_places" value="Network Places" }
{ assign var="t_edit" value="Edit" }
{ assign var="t_remove" value="Remove" }
{ assign var="t_new_network_place" value="New Network Place" }
{ assign var="t_help_box_places" value="Help Box Places" }
{ assign var="t_first_enabled_module" value="first enabled module" }
{ assign var="t_advanced_settings_sources" value="advanced settings sources" }

<h2><a href="#">{$software_version}</a></h2>


{if $enabled}
    <fieldset>
        <legend>{$translations[$t_network_places]}</legend>
        <div id="dlp_network_places">

<div class="help_box">
    {$translations[$t_help_box_places]}
</div>

            {foreach from=$network_places item=network_place}
                <div class="element" id="{$network_place._id}">
                    <div class="info">
                        <div class="element-name">{$network_place.description}</div>
                    </div>
                    <div class="controls">
                        <form action="" method="get">
                            <input type="hidden" name="idNetworkPlace" value="{$network_place.id}">
                            <input type="hidden" name="module" value="element">
                            <input type="hidden" name="action" value="block">
                            <a class="button mr_10 ml_10" href="?module=dlp&action=editNetworkPlace&idNetworkPlace={$network_place._id}">{$translations[$t_edit]}</a>
                            <a class="button red" href="?module=dlp&action=deleteNetworkPlace&idNetworkPlace={$network_place._id}">{$translations[$t_remove]}</a>
                        </form>

                    </div>
                </div>
            {/foreach}
        </div>
    </fieldset>

    <div class="button_container">
        <a class="button" href="?module=dlp&action=newNetworkPlace">{$translations[$t_new_network_place]}</a>
    </div>

{else}
    <br/><br/>
    <div>
        {$translations[$t_first_enabled_module]} <a href="?module=dlp&action=showAdvanced">{$translations[$t_advanced_settings_sources]}</a>.
    </div>
{/if}

