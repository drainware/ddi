{ assign var="t_remote_devices" value="Remote Devices" }
{ assign var="t_help_box_remote_devices" value="Help Box Remote Devices" }
{ assign var="t_number_devices_found" value="Number devices Found" }
{ assign var="t_map_view" value="Map View" }
{ assign var="t_network_view" value="Network View" }



<h2><a href="#">{$software_version}</a></h2>
<h3>{$translations[$t_remote_devices]}</h3>

<div class="help_box">
    {$translations[$t_help_box_remote_devices]}
</div>
<br />

<div id="nro_devices" style="width: 260px;"> {$translations[$t_number_devices_found]} </div>

<div>
    <input type="hidden" value="{$remote_devices_id}" id="remote_devices_id" />
    <input type="button" value="{$translations[$t_map_view]}" disabled="disabled" id="map_view_button" class="button" />
    <input type="button" value="{$translations[$t_network_view]}" id="network_view_button" class="button" />
</div>

<div id="network_view" style="display:none;">
    <h5 id="endpoint_network"> </h5>
</div>


<div id="map_view">
    <div id="endpoint_map"></div>
</div>
