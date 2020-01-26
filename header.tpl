<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">


<html>
    <head>
	<link rel="icon"
	  type="image/ico" 
	  href="/drainware.ico">

        <title>Drainware</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, user-scalable=yes">

        <!-- <link rel="stylesheet" type="text/css" href="style.css" /> -->
        <link rel="stylesheet" type="text/css" href="ribbon.css" /> 

        <link rel="stylesheet" type="text/css" href="{$page}.css" />
        <link rel="stylesheet" type="text/css" href="ddi/css/colorbox.css" />


        <!--[if lt IE 9]>
        <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
        <![endif]-->

        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.10.2.custom.min.js"></script>
        <script type="text/javascript" src="js/jquery.placeholder.js"></script>
        <script type="text/javascript" src="ddi/js/jquery.colorbox.js"></script>
        <script type="text/javascript" src="js/{$page}.js"></script>

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        
                <!--<script type="text/javascript" src="js/dlp.js"></script>-->

	{literal}
        <script type="text/javascript">
          var trak=trak||[];trak.io=trak.io||{};trak.io.load=function(e){var t=document.createElement("script");t.type="text/javascript",t.async=!0,t.src=("https:"===document.location.protocol?"https://":"http://")+"d29p64779x43zo.cloudfront.net/v1/trak.io.min.js";var n=document.getElementsByTagName("script")[0];n.parentNode.insertBefore(t,n);var r=function(e){return function(){trak.push([e].concat(Array.prototype.slice.call(arguments,0)))}},i=["initialize","identify","track","alias","channel","source","host","protocol","page_view"];for(var s=0;s<i.length;s++) trak.io[i[s]]=r(i[s]);trak.io.initialize.apply(trak.io,arguments)};
          trak.io.load('571464b2396e32e0de8f2e87a18afea91b438c6a');
        </script>
	{/literal}

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
  trigger_background_color: '#e23a39',
  position: 'top',
  screenshot_enabled: false
}]);

// Identify the user and pass traits
// To enable, replace sample data with actual user traits and uncomment the line
UserVoice.push(['identify', {
  //email:      'john.doe@example.com', // User’s email address
  //name:       'John Doe', // User’s real name
  //created_at: 1364406966, // Unix timestamp for the date the user signed up
  //id:         123, // Optional: Unique id of the user (if set, this should not change)
  //type:       'Owner', // Optional: segment your users by type
  //account: {
  //  id:           123, // Optional: associate multiple users with a single account
  //  name:         'Acme, Co.', // Account name
  //  created_at:   1364406966, // Unix timestamp for the date the account was created
  //  monthly_rate: 9.99, // Decimal; monthly rate of the account
  //  ltv:          1495.00, // Decimal; lifetime value of the account
  //  plan:         'Enhanced' // Plan name for the account
  //}
}]);

// Add default trigger to the bottom-right corner of the window:
//UserVoice.push(['addTrigger', { mode: 'contact', trigger_position: 'top-right' }]);

// Or, use your own custom trigger:
UserVoice.push(['addTrigger', '#uservoice_box', { mode: 'contact' }]);

// Autoprompt for Satisfaction and SmartVote (only displayed under certain conditions)
//UserVoice.push(['autoprompt', {}]);
</script>
{/literal}

{literal}
        <script>
            $(function(){
              // bind change event to select
              $('#dynamic_select').bind('change', function () {
                  var url = $(this).val(); // get selected value
                  if (url) { // require a URL
                      window.location = url; // redirect
                  }
                  return false;
              });
            });
        </script>
{/literal}
    </head>
    <body>



        <div class="panel">

            <!--<form action="http://www.drainware.com/ddi/?module=main&action=login" method="POST" class="container">
                <p><input type="text" placeholder="username" name="name" /> <input type="password" name="passwd" placeholder="password" />
                                <input name="redirect_to" type="hidden" value="/ddi/index.php"> <button type="submit">Login</button> <a href="/ddi/?module=cloud&action=recoveryPassword">Olvidé mi clave</a></p>
            </form>-->
            <nav> 
                        <ul>
       <div class="ribbon-wrapper"><div class="ribbon">BETA</div></div>

                                <h1><a href="/">Drainware cloud</a></h1>
                                <li>
                                        <a  href="{$prefix}#data">DLP</a>
                                </li>
                                <li>
                                        <a  href="{$prefix}#forensics">Inspector</a>
                                </li>
                                <li>
                                        <a  href="{$prefix}#sandbox">Sandbox</a>
                                </li>
                                <li>
                                        <a  href="{$prefix}#ejemplos">Learn More</a>
                                </li>
                                <li class="sign-up">

					<a  href="https://www.paywithatweet.com/pay/?id=c50dd785e353b71bad67a1125d0fcf4e"
                                        onclick="window.open(this.href, 'mywin','left=200,top=200,width=850,height=460,toolbar=1,resizable=0'); return false;" >Sign up</a>
{*


                                        <a  href="/register.html"
                                        onclick="window.open(this.href, 'mywin','left=200,top=200,width=850,height=400,toolbar=1,resizable=0'); return false;" >Sign up</a>
*}

                                </li>
                                <li class="login">
                                        <a  href="https://www.drainware.com/ddi">Login</a>
                                </li>
                        </ul>



                        <div id="Dw"><h1><a href="./Drainware_files/Drainware.htm">Drainware cloud</a></h1></div>
                        <select id="dynamic_select"> 
                          <option value="" selected="selected">Menú</option>          
                          <option value="{$prefix}#data">DLP</option> 
                          <option value="{$prefix}#forensics">Inspector</option> 
                          <option value="{$prefix}#sandbox">Sandbox</option> 
                          <option value="{$prefix}#ejemplos">Learn More</option> 
                        </select> 

                        <div id="Dw1"><li class="sign-up">
                          <a href="https://www.paywithatweet.com/pay/?id=c50dd785e353b71bad67a1125d0fcf4e" onclick="window.open(this.href, &#39;mywin&#39;,&#39;left=200,top=200,width=850,height=460,toolbar=1,resizable=0&#39;); return false;">Sign up</a>
                                </li>
                                <li class="login">
                                        <a href="https://www.drainware.com/ddi">Login</a>
                                </li>
                        </div>  

                        <div id="Dw2">
                                <li>
                                        <a  href="{$prefix}#data">DLP</a>
                                </li>
                                <li>
                                        <a  href="{$prefix}#forensics">Inspector</a>
                                </li>
                                <li>
                                        <a  href="{$prefix}#sandbox">Sandbox</a>
                                </li>
                                <li>
                                        <a  href="{$prefix}#ejemplos">Learn More</a>
                                </li>
                        </div>
                </nav>

        </div><!-- panel -->

{if $page eq "main" }
        <div class="intro" id="intro">
            <div class="container">

                <div>

                    <iframe src="https://player.vimeo.com/video/69536755?title=0&amp;byline=0&amp;portrait=0&amp;color=f00000" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>

                </div>


                                <a  href="https://www.paywithatweet.com/pay/?id=c50dd785e353b71bad67a1125d0fcf4e" onclick="window.open(this.href, 'mywin','left=200,top=200,width=850,height=460,toolbar=1,resizable=0'); return false;" >



{*
                                <a  href="/register.html" onclick="window.open(this.href, 'mywin','left=200,top=200,width=850,height=400,toolbar=1,resizable=0'); return false;" >

*}
                                <p>Get it for free!     </p>

                                        <label>*Just pay with a tweet</label>
                                        </a>
            </div><!-- conteiner -->
        </div><!-- intro -->
{/if}
