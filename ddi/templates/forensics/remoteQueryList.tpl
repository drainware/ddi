{ assign var="t_return" value="Return" }
{ assign var="t_search_list" value="Search Report" }

<br/>

{$msg}

<br/>    

<div>
    <p style="width: 40%">
        <a class="prev button red" href="?module=forensics&action=showMultipleRemoteSearch">{$translations[$t_return]}</a>
        <a class="next button" href="?module=reporter&action=showSearchReports">{$translations[$t_search_list]}</a>
    </p>
</div>
