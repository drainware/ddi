{ assign var="t_web_filter_configuration" value="Web Filter Configuration" }

<br />
<div id="content_info">
    <h2>{$translations[$t_web_filter_configuration]} - {$group_name}</h2>    
</div>
<div class="separator_header">
    {include file="webfilter/categories.tpl" }
    <br />
    {include file="webfilter/extensions.tpl" }
    <br />
    {include file="webfilter/lists.tpl" }
    <br />
</div>