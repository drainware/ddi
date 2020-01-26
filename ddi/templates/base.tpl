<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">

<head>
    <link rel="icon"
      type="image/ico"
      href="/drainware.ico">
    <meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
    <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
    Remove this if you use the .htaccess -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title>Drainware {$module|capitalize} {$action|capitalize}</title>

    <meta name="author" content="Drainware, Inc. " />
    <meta name="keywords" content="content filter" />
    <meta name="description" content="Drainware" />

<!-- Facebook Meta Tags -->
    <meta property="og:title" content="Drainware: Data Leak Prevention"/>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{$smarty.server.HTTP_HOST}{$smarty.server.REQUEST_URI}"/>
    <meta property="og:image" content="http://www.drainware.com/img/cloud_logo_color.png"/>
    <meta property="og:description"
          content="Drainware is a security service developed to 
                   provide control over the sensitive and provide 
                   information."/>
    <meta name="viewport" content="width=device-width, user-scalable=no">



    <link type="text/css" rel="stylesheet" href="css/drainware_v2.css?v=1" type="text/css" media="screen, projection" />
    {include file="$container/css.tpl" }
    <link type="text/css" rel="stylesheet" href="js/jqplot/jquery.jqplot.min.css"/>
    <link type="text/css" rel="stylesheet" href="css/flexigrid.css"/>
    <link type="text/css" rel="stylesheet" href="css/reporter.css"/>
    <link type="text/css" rel="stylesheet" href="css/smoothness/jquery-ui-1.8.16.custom.css" />
    <link type="text/css" rel="stylesheet" href="css/colorbox.css" />
    <link type="text/css" rel="stylesheet" href="css/FileExplorer.css" />
    <link type="text/css" rel="stylesheet" href="css/RemoteDevices.css" />

    <link type="text/css" rel="stylesheet" href="js/notifications/ui.notify.css" />

    {literal}
    <!--[if gte IE 8]>
    <style>
    fieldset.accordion { border:0; }
    fieldset.accordion legend { border:1px solid #cccccc; } 
    </style>
    <![endif]-->
    {/literal}



    {literal}
	<script type="text/javascript">
	  var trak=trak||[];trak.io=trak.io||{};trak.io.load=function(e){var t=document.createElement("script");t.type="text/javascript",t.async=!0,t.src=("https:"===document.location.protocol?"https://":"http://")+"d29p64779x43zo.cloudfront.net/v1/trak.io.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(t,n);var r=function(e){return function(){trak.push([e].concat(Array.prototype.slice.call(arguments,0)))}},i=["initialize","identify","track","alias","channel","source","host","protocol","page_view"];for(var s=0;s<i.length;s++) trak.io[i[s]]=r(i[s]);trak.io.initialize.apply(trak.io,arguments)};
	</script>
    {/literal}

	<script type="text/javascript">
	  trak.io.load('571464b2396e32e0de8f2e87a18afea91b438c6a', {ldelim} distinct_id: '{$acccount_mail}' {rdelim});
	</script>

    {* preload hack commented (to enable, uncomment  also load.js lines) 

    {if $action!="showRemoteDevices"}
        {if $module != "reporter" || $action=="showSearchReports"}
            {literal}
                <style>
                    #main_content{
                        display:none;
                    }
                </style>
            {/literal}
        {else}
            {literal}
                <style>
                    fieldset:first-child{
                        display:none;
                    }
                    h3{
                        display:none;
                    }
                </style>
            {/literal}
        {/if}
    {/if}
    *}

</head>

<body>
    {assign var="information" value="Information"}
    {assign var="modules" value="Modules"}
    {assign var="filter_type" value="Type of Filter"}
    {assign var="block_categories" value="Block Categories"}
    {assign var="block_extensions" value="Block Extensions"}
    {assign var="firewall" value="Firewall"}
    {assign var="lists" value="Lists"}
    {assign var="block_report" value="Block Report"}
    {assign var="access_report" value="Access Report"}
    {assign var="tlogout" value="logout"}
    {assign var="dlp_files" value="Files"}
    {assign var="dlp_rules" value="Rules"}
    {assign var="dlp_stats" value="Stats"}
    {assign var="configuration" value="Configuration" }
    {assign var="advanced_configuration" value="Advanced Configuration"}
    {assign var="reboot" value="Reboot"}
    {assign var="reboot_message" value="Reboot Message"}
    {assign var="t_search_help_topic" value="Search for help topics..."}

    <input id="notification_status" type="hidden" value="{$notification_status}" />
    
    <div id="notifications_placeholder">
    {foreach from=$msg_notifications_bar item=msg_notification}
	<div class="{$msg_notification->getType()} notification_bar">
		{assign var="msg" value=$msg_notification->getMessage()}
	    {$translations[$msg]}
	</div>

    {/foreach}
    </div>
    
    {literal}
        <div id="notification_container" style="display:none">
            <div id="default">
                <h1>#{title}</h1>
                <p>#{text}</p>
            </div>

            <div id="sticky">
                <a class="ui-notify-close ui-notify-cross" href="#">x</a>
                <h1>#{title}</h1>
                <p>#{text}</p>
            </div>

            <div id="withIcon">
                <a class="ui-notify-close ui-notify-cross" href="#">x</a>
                <div style="float:left;margin:0 10px 0 0"><img src="#{icon}" alt="warning" /></div>
                <h1>#{title}</h1>
                <p>#{text}</p>
            </div>
        </div>
    {/literal}

    <div id="minHeight"></div>
    <div id="wrapper" class="{$smarty.get.module} {$action}">
        {if $top_menu eq 1 }
            <div id="header-wrap">
                <div id="header">
                    <a id="logo_header" title="Drainware">Drainware</a>

                    <!--<h1 id="logo"><a href="/" title="drainware">drain<span class="grey">ware</span></a></h1>-->
                    <!--<div {if $sbmode eq "unique"}style="display:none;"{/if}>-->

                    <!--
                    <ul class="js" id="header_nav">
                    {foreach from=$menu item=menu_item}
                    <li><a href="?module={$menu_item}">{$menu_item}</a></li>
                    {/foreach}
                    </ul>
                    -->

                    {if $smarty.session.authorized eq "1"}
                        <div id="header_options">
                            <ul id="header_nav">

                                { if $smarty.get.module eq "user"  or $smarty.get.module eq "group" }
                                {assign var="display_main" value="1"}
                                { /if }
                                {foreach from=$menu_translated item='menu_item' key='i'}
                                    <!-- FIXME: We should merge group and user modules into main, now it is quite confusing -->
                                    { if $menu[$i] neq "user" and $menu[$i] neq "group" }
                                    <li><a class="{$menu[$i]}{if $smarty.get.module eq $menu[$i]} current{/if}{if $display_main eq "1" and $menu[$i] eq "main" } current{/if}" title="{$menu_item}" href="?module={$menu[$i]}">{$menu_item}</a></li>
                                    { /if }
                                {/foreach}
                            </ul>

                            <!-- search engine  -->

                            <div id="header_search_form">
                                <input type="text" name="search" id="search_input" placeholder="{$translations[$t_search_help_topic]}" />
                                <img id="guide_search" src="images/search_icon.png" alt="buscar" class="guide_search"/>
                            </div>
                            <a class="logout" title="{$translations[$tlogout]}" href="?module=main&action=logout"></a>  

                        </div><!-- #header_options -->
                    {/if}

                </div><!-- #header -->
            </div><!-- #heeader_wrap -->
        {/if}
        <div id="wrap">

            <div id="wrap_content">
                {if $sidebar_menu eq 1}
                    <div id="sidenav" class="equal_column">
                        {include file="$container/menu.tpl" }
                    </div><!-- #sidenav -->
                {/if}

                <div id="main_content" class="equal_column">

                      {if $sidebar_menu eq 1}
                    <div id="sidebutton"><img src="images/sidebutton.png" alt="toogle sidebar"></div>
                    {/if}

                    <div id="content_body">
                        <!--<div id="slogan"></div>-->
                        <div class="notifications-area" id="notifications-area"></div>
                        {include file="$container/$action.tpl" }
                    </div>

                    <!--   <div id="content_ads">
                      
               
                    {foreach from=$news item=news_item}
                        <div class="ads_section">
                          <a target="_blank" href="{$news_item.link}"><h2>{$news_item.title}</h2></a>
                          <p>{$news_item.desc}</p>
                        </div>
                    {/foreach}
                    -->


                </div>
            </div>

        </div><!-- #warp_content -->

    </div>
</div>

{if $footer eq 1}
    <div id="footer_wrap">
        <div id="footer">
            <p>Copyright 2014&nbsp;<a href="/">Drainware, Inc.</a></p>
            <a id="logo_footer">Drainware</a>
        </div><!-- footer -->
    </div><!-- footer-wrap -->
{/if}



<script src="js/lib/jquery.js"></script>
<script type="text/javascript" src="js/load.js"></script>
<script src="js/lib/jquery.tipsy.js?v=1" type="text/javascript"></script>
<script src="js/lib/formToWizard.js?v=1" type="text/javascript"></script>
<script src="js/lib/jquery.autotab_1.1b.js?v=1" type="text/javascript"></script>
<script src="js/assistant.js?v=1" type="text/javascript"></script>

<script type="text/javascript" src="js/lib/jquery.validate.min.js?v=1"></script>
<script type="text/javascript" src="js/lib/additional-methods.min.js?v=1"></script>

<script type="text/javascript" src="js/d3/d3.js"></script>
<script type="text/javascript" src="js/d3/d3.layout.js"></script>
<script type="text/javascript" src="js/Concurrent.Thread.js"></script>
<script type="text/javascript" src="js/flexigrid.js"></script>
<script type="text/javascript" src="js/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.colorbox.js"></script>
<script type="text/javascript" src="js/sidenav.js"></script>
<script type="text/javascript" src="js/notifications/jquery.notify.js"></script>
<script type="text/javascript" src="js/notifications/desktop-notify.js"></script>


<script type="text/javascript" src="js/dw.js?v=1"></script>

<script type="text/javascript" src="https://maps.google.com/maps/api/js?sensor=true&key=AIzaSyD8xIha3tSDHJBVSUMu12605eObCJBPhKc"></script>

{literal} 
<script>
// Include the UserVoice JavaScript SDK (only needed once on a page)
UserVoice=window.UserVoice||[];(function(){var uv=document.createElement('script');uv.type='text/javascript';uv.async=true;uv.src='//widget.uservoice.com/GSFqBL8j5EFrGrez6Wg4w.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(uv,s)})();

//
// UserVoice Javascript SDK developer documentation:
// https://www.uservoice.com/o/javascript-sdk
//

// Set colors
UserVoice.push(['set', {
  accent_color: '#e23a39',
  trigger_color: 'white',
  trigger_background_color: '#e23a39'
}]);

{/literal}

UserVoice.push(['identify', {ldelim} 
  email:      '{$acccount_mail}', 
  type:       'admin', 
  account: {ldelim}
    id:           '{$smarty.session.license}', 
    name:         '{$account_company}', 
    created_at:   '{$account_creation}', 
    monthly_rate: '{$account_monthly_rate}', 
    ltv:          '{$account_ltv}', 
    plan:         '{$client_type}' 
  {rdelim} 
{rdelim}]);

{literal}
// Add default trigger to the bottom-right corner of the window:
UserVoice.push(['addTrigger', { mode: 'contact', trigger_position: 'bottom-right' }]);

// Or, use your own custom trigger:
//UserVoice.push(['addTrigger', '#id', { mode: 'contact' }]);

// Autoprompt for Satisfaction and SmartVote (only displayed under certain conditions)
UserVoice.push(['autoprompt', {}]);
</script>
{/literal} 

{literal}
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-20509882-16', 'drainware.com');
      ga('send', 'pageview');
    </script>
{/literal}

{include file="$container/js.tpl" }

</body>

{literal} 

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-20509882-12']);
  _gaq.push(['_setDomainName', 'drainware.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

{/literal} 

</html>
