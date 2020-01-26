{ if $state.error neq "true" }
{include file="utils/ok.tpl" }
{else}
{include file="utils/error.tpl" }
{ /if }