{ assign var="t_ok_1" value="Ok Text 1" }
{ assign var="t_ok_2" value="Ok Text 2" }

<h2>{$translations[$t_ok_1]}</h2>

<br />

{$translations[$t_ok_2]}

<meta http-equiv="refresh" content="5;url=?{foreach from=$return_to key=var item=value}{$var}={$value}&{/foreach}">
