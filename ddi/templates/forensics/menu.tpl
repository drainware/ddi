{ assign var="t_remote_search" value="Remote Search" }
{ assign var="t_multiple_remote_search" value="Multiple Remote Search" }
{ assign var="t_remote_file_explorer" value="Remote File Explorer" }
{ assign var="t_remote_devices" value="Remote Devices" }


<ul>
    {if $smarty.get.action eq "show" || $smarty.get.action eq ''}
        <li><a id="opt_forensics_remote_search" class="active" href="#"><span>{$translations[$t_remote_search]}</span></a></li>
         <li><a id="opt_forensics_search_list" href="?module=forensics&action=showMultipleRemoteSearch"><span>{$translations[$t_multiple_remote_search]}</span></a></li>
        <li><a id="opt_forensics_file_explorer" href="?module=forensics&action=showRemoteFileExplorer"><span>{$translations[$t_remote_file_explorer]}</span></a></li>
        <li><a id="opt_forensics_remote_devices" href="?module=forensics&action=showRemoteDevices"><span>{$translations[$t_remote_devices]}</span></a></li>
    {elseif $smarty.get.action eq "showMultipleRemoteSearch"}
        <li><a id="opt_forensics_remote_search" href="?module=forensics&action=show"><span>{$translations[$t_remote_search]}</span></a></li>
        <li><a id="opt_forensics_search_list" class="active" href="#"><span>{$translations[$t_multiple_remote_search]}</span></a></li>
        <li><a id="opt_forensics_file_explorer" href="?module=forensics&action=showRemoteFileExplorer"><span>{$translations[$t_remote_file_explorer]}</span></a></li>
        <li><a id="opt_forensics_remote_devices" href="?module=forensics&action=showRemoteDevices"><span>{$translations[$t_remote_devices]}</span></a></li>
    {elseif $smarty.get.action eq "showRemoteFileExplorer"}
        <li><a id="opt_forensics_remote_search" href="?module=forensics&action=show"><span>{$translations[$t_remote_search]}</span></a></li>
        <li><a id="opt_forensics_search_list" href="?module=forensics&action=showMultipleRemoteSearch"><span>{$translations[$t_multiple_remote_search]}</span></a></li>
        <li><a id="opt_forensics_file_explorer" class="active" href="#"><span>{$translations[$t_remote_file_explorer]}</span></a></li>
        <li><a id="opt_forensics_remote_devices" href="?module=forensics&action=showRemoteDevices"><span>{$translations[$t_remote_devices]}</span></a></li>
    {elseif $smarty.get.action eq "showRemoteDevices"}
        <li><a id="opt_forensics_remote_search" href="?module=forensics&action=show"><span>{$translations[$t_remote_search]}</span></a></li>
        <li><a id="opt_forensics_search_list" href="?module=forensics&action=showMultipleRemoteSearch"><span>{$translations[$t_multiple_remote_search]}</span></a></li>
        <li><a id="opt_forensics_file_explorer" href="?module=forensics&action=showRemoteFileExplorer"><span>{$translations[$t_remote_file_explorer]}</span></a></li>
        <li><a id="opt_forensics_remote_devices" href="?module=forensics&action=showRemoteDevices"><span>{$translations[$t_remote_devices]}</span></a></li>        
    {else}
        <li><a id="opt_forensics_remote_search" href="?module=forensics&action=show"><span>{$translations[$t_remote_search]}</span></a></li>
        <li><a id="opt_forensics_search_list" href="?module=forensics&action=showMultipleRemoteSearch"><span>{$translations[$t_multiple_remote_search]}</span></a></li>
        <li><a id="opt_forensics_file_explorer" href="?module=forensics&action=showRemoteFileExplorer"><span>{$translations[$t_remote_file_explorer]}</span></a></li>
        <li><a id="opt_forensics_remote_devices" href="?module=forensics&action=showRemoteDevices"><span>{$translations[$t_remote_devices]}</span></a></li>
    {/if}

</ul>
