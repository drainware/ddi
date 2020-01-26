{ assign var="manage_segments" value="Manage Segments" }
{ assign var="segments" value="Segments" }
{ assign var="new_segment" value="New Segment" }
{ assign var="block" value="Block" }
{ assign var="unblock" value="Unblock" }
{ assign var="edit" value="Edit" }
{ assign var="remove" value="Remove" }

<h2><a href="#">{$software_version}</a></h2>
<h2>{$translations[$manage_segments]}</h2>

<fieldset>

  <legend>{$translations[$segments]}</legend>


  <div id="rooms">
    {foreach from=$rooms item=room}
      {if $room.status eq blocked}
        <div class="room blocked">
      {else}
        <div class="room">
      {/if}
      <div class="info">
        <div class="room-name">{$room.name}</div>
        <div class="room-desc">{$room.desc}</div>
      </div>

      <div class="controls">

        <form action="" method="get">
          <input type="hidden" name="idRoom" value="{$room._id}">
          <input type="hidden" name="module" value="network">

          {if $room.status eq blocked}
            <input type="hidden" name="action" value="unblockRoom">
            <input class="button green" type="submit" value="{$translations[$unblock]}">
          {else}
            <input type="hidden" name="action" value="blockRoom">
            <input class="button orange" type="submit" value="{$translations[$block]}">
            <a class="button mr_10 ml_10" href="?module=network&action=editRoom&idRoom={$room._id}">{$translations[$edit]}</a>
            <a class="button red" href="?module=network&action=deleteRoom&idRoom={$room._id}">{$translations[$remove]}</a>
          {/if}

        </form>

      </div>
        </div>
    {/foreach}

    <div class="button_container">
      <a class="button" href="?module=network&action=newRoom">{$translations[$new_segment]}</a>
    </div>

  </div>

</fieldset>
