{ assign var="web_filter_config" value="Web Filter Configuration" }

<fieldset id="profiles">
    <legend>{$translations[$web_filter_config]}</legend>
    {foreach key=key item=group from=$groups}
        <p>
        <span class="entitle"><a href="?module=webfilter&action=detail&name={$group.name}">{$group.name}</a></span><small>{$group.path|truncate:60}</small>    </p>
    {/foreach}
</fieldset>
