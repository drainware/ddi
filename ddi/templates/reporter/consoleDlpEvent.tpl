{ assign var="t_date" value="Date" }
{ assign var="t_user" value="User" }
{ assign var="t_groups" value="group" }
{ assign var="t_origin" value="Origin" }
{ assign var="t_concept" value="Concept" }
{ assign var="t_policies" value="Policies" }
{ assign var="t_here" value="here" }
{ assign var="t_location" value="Location" }
{ assign var="t_action" value="Action" }
{ assign var="t_severity" value="Severity" }
{ assign var="t_application" value="Application" }
{ assign var="t_leak_information" value="Leak attempt information" }
{ assign var="t_configure_screeshot" value="You have to configure the screenshot severity" }
{ assign var="t_maximun_screeshot" value="You reached the maximum number of screenshot of" }
{ assign var="t_month_upgrade" value="that month. You can upgrade to premium to view all" }
{ assign var="t_screenshots_future" value="the screenshots on the future" }
{ assign var="t_match" value="Match" }
{ assign var="t_module" value="Module" }
{ assign var="t_filename" value="Filename" }
{ assign var="t_map" value="Map" }
{ assign var="t_context" value="Context" }
{ assign var="t_previous_event" value="Previous Event" }
{ assign var="t_next_event" value="Next Event" }
{ assign var="t_back" value="Back" }


<h3>{$translations[$t_leak_information]}</h3>
        <ul class="double-impar">
            <li><strong>{$translations[$t_date]}</strong></li> <li>{$event->getDate()|date_format:"%B %e, %Y at %H:%M:%S  "}</li>
        </ul>
        <ul class="double">
            <li><strong>IP</strong></li> <li>{$event->getIP()}</li>
        </ul>
        <ul class="double-impar">
            <li><strong>{$translations[$t_user]}</strong></li> <li>{$event->getUser()}</li>
        </ul>
        <ul  class="double">
            <li><strong>{$translations[$t_groups]}</strong></li> <li>{foreach name=groups from=$event->getGroups() item=group} {$group}{/foreach}</li>
        </ul>
        <ul class="double-impar">    
            <li><strong>{$translations[$t_origin]}</strong></li> <li>{$event->getOrigin()} <img src="images/origin-{$event->getOrigin()}.png" /> </li>
        </ul>
        <ul  class="double">
            <li><strong>{$translations[$t_policies]}</strong></li> <li>{foreach name=policies from=$event->getPolciesName() item=policy}{$policy}{/foreach}</li>
        </ul>
        <ul class="double-impar">    
            <li> {if $event->getConceptName()} <strong>{$translations[$t_concept]}</strong></li> {/if} <li>{$event->getConceptName()} </li>  
        </ul>
            <br/>
                {assign var="screenshot" value=$event->getScreenshot()}            
                {if $screenshot neq '/ddi/images/drainware_screen.png'} 
                    <a class="gallery" href="{$screenshot}"><img src="{$screenshot}"  height="210" width="280"  title="{$event->getDate()|date_format:"%A, %B %e, %Y"}" /></a>
                    {else}
                    <img src="{$screenshot}" height="210" width="280" title="{$event->getDate()|date_format:"%A, %B %e, %Y"}" />
                    <br/><br/>   
                    {if $cloud_user_type == 'premium'}
                        <p>{$translations[$t_configure_screeshot]}<a href="?module=dlp&action=showAdvanced">{$translations[$t_here]}</a></p>
                    {else}
                        <p>{$translations[$t_maximun_screeshot]}</p>
                        <p>{$translations[$t_month_upgrade]}</p>
                        <p>{$translations[$t_screenshots_future]} <a href="?module=main&action=showCloudConfig">{$translations[$t_here]}</a>.</p>
                    {/if}
                {/if}
            <br/>
        <ul  class="double-impar">
            <li><strong>{$event->getType()}</strong></li> <li>{$event->getIdentifier()} </li>
        </ul>
        <ul class="double">      
            <li><strong>{$translations[$t_match]}</strong></li><li> {if $event->checkContext()} <a id="see_context" class="inline cboxElement" href="#event_context"> {$event->getMatch()} </a> {else} {$event->getMatch()} {/if} </li>
        </ul>
        <ul class="double-impar">
            <li><strong>{$translations[$t_action]}</strong></li> <li> {$event->getAction()}</li>
        </ul>
        <ul class="double">
            <li><strong>{$translations[$t_severity]}</strong></li> <li>{$event->getSeverity()} <img src="images/severity-{$event->getSeverity()}.gif"></li>
        </ul>
        <ul class="double-impar">
            <li><strong>{$translations[$t_module]}</strong></li> <li>{$event->getTextOrigin()}</li>
        </ul>
        <ul class="double">
        {if $event->getApp()}
            
                <li><strong>{$translations[$t_application]}</strong></li><li>{$event->getApp()}</li>
            </ul >
            {if $event->getFilename()}
                <ul>
                    <li><strong>{$translations[$t_filename]}</strong></li><li> {$event->getFilename()}</li>
                </ul>
        <ul class="double-impar">
                    <li><strong>{$translations[$t_location]}</strong></li><li> <a id="see_map" class="inline cboxElement" href="#event_map">{$translations[$t_map]}</a> </li>
                </ul>
            {else}
                <ul class="double">
                    <li><strong>{$translations[$t_location]}</strong></li><li> <a id="see_map" class="inline cboxElement" href="#event_map">{$translations[$t_map]}</a> </li>
        </ul>
        <ul class="double-impar">                 
            {/if}
        {else}
           
                <li> <strong>{$translations[$t_filename]}</strong> </li> <li>{$event->getFilename()}</li>
            </ul>
            <ul class="double-impar">
                <li> <strong>{$translations[$t_location]}</strong> </li> <li> <a id="see_map" class="inline cboxElement" href="#event_map">{$translations[$t_map]}</a> </li>
            </ul>
        {/if}


<div id="event_map_view" style="display:none">    
    <input type="hidden" value="{$event->getLat()}" id="event_lat" />
    <input type="hidden" value="{$event->getLng()}" id="event_lng" />
    <input type="hidden" value="{$event->getAccuracy()}" id="event_accuracy" />
    <div id="event_context" class="event-context" style="padding:10px; background:#fff;">
        <fieldset>
            <legend>{$translations[$t_context]}</legend>
            <p>{$event->getContext()}</p>
        </fieldset>

    </div>
    <div id="event_context_1" style="width:50%;"> {$event->getContext()} </div>
    <div id="event_map" style="width:998px; height: 500px; padding:10px; background:#fff;"></div>
</div>

{if $event->getPreviousEvent()}
    <a class="prev button" href="/ddi/?module=reporter&action=consoleDlpEvent&event_id={$event->getPreviousEvent()}">&lt; {$translations[$t_previous_event]}</a>
{/if}
{if $event->getNextEvent()}
    <a class="next button" href="/ddi/?module=reporter&action=consoleDlpEvent&event_id={$event->getNextEvent()}">{$translations[$t_next_event]} &gt;</a>
{/if}

<br/><br/><br/>

<p>
    <a class="prev button orange" href="{$dlp_report_search}">{$translations[$t_back]}</a>
</p>

