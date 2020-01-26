{ assign var="thosts" value="Hosts" }
{ assign var="new_host" value="New Host" }
{ assign var="sub_network" value="Subnet" }
{ assign var="gateway" value="Gateway" }
{ assign var="new_host" value="New Host" }
{ assign var="remove" value="Remove" }
{ assign var="change" value="Change" }
{ assign var="password" value="Password" }
{ assign var="repeat_please" value="Repeat, please" }
{ assign var="rangea" value="Range From" }
{ assign var="rangeb" value="Range To" }

<h2><a href="#">{$software_version}</a></h2>

<div id="wrapper-hosts">



  <fieldset>
    <legend>{$translations[$thosts]}</legend>

    <div id="hosts">

      {foreach from=$hosts item=host}

        <div class="hosts">
          <div class="host">

              {if $host.type eq "ip"}
              <span class="host-label">Ip:</span>
              <span class="host-value">{$host.host}</span>
              {else if $host.type eq "range" }       
              <span class="host-label">{$translations[$rangea]}:</span>
              <span class="host-value">{$host.host[0]}</span>
              <span class="host-label">{$translations[$rangeb]}:</span>
              <span class="host-value">{$host.host[1]}</span>
              {/if}


          </div>

          <div class="controls">
            <form action="?module=network&action=deleteHost" method="post">
              <a class="button red" onclick="$(this).submit()">{$translations[$remove]}</a>
	      <input type="hidden" name="id" value="{$host._id}">
            </form>

          </div>

        </div>

      {/foreach}

    </div>

    <div class="button_container">
      <a class="button" href="?module=network&action=newHost">{$translations[$new_host]}</a>
    </div>

  </fieldset>

</div>
