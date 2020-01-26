{ assign var="segments" value="Segments" }
{ assign var="segment_name" value="Segment Name" }
{ assign var="segment_description" value="Segment Description" }
{ assign var="range_from" value="Range From" }
{ assign var="range_to" value="Range To" }
{ assign var="save" value="Save" }
{ assign var="reset" value="Reset" }


<h2><a href="#">{$software_version}</a></h2>
<fieldset>

	<legend>{$translations[$segments]}</legend>

  <div id="form-rooms">
    <form action="" method="get">
      <input type="hidden" name="idRoom" value="{$room.id}">
      <input type="hidden" name="module" value="network">
      <input type="hidden" name="action" value="updateRoom">

      <p>
        <span class="entitle_medium">{$translations[$segment_name]}:</span>
        <input type="text" class="text medium required" name="name" id="name" value="{$room.name}">
      </p>

      <p>
        <span class="entitle_medium">{$translations[$segment_description]}:</span>
        <textarea name="desc" class="text required" id="desc" rows="3" cols="30">{$room.desc}</textarea>
      </p>

      <p>
        <span class="entitle_medium">{$translations[$range_from]} : </span>
        <input type="text" class="ipgroup text short required" max="255" name="group1a" maxlength="3" size="3" value="{$ipa[0]}">.
        <input type="text" class="ipgroup text short required" max="255" name="group2a" maxlength="3" size="3" value="{$ipa[1]}">.
        <input type="text" class="ipgroup text short required" max="255" name="group3a" maxlength="3" size="3" value="{$ipa[2]}">.
        <input type="text" class="ipgroup text short required" max="255" name="group4a" maxlength="3" size="3" value="{$ipa[3]}">
      </p>
      <p>
        <span class="entitle_medium">{$translations[$range_to]} :</span>
        <input type="text" class="ipgroup text short required" max="255" name="group1b" maxlength="3" size="3" value="{$ipb[0]}">.
        <input type="text" class="ipgroup text short required" max="255" name="group2b" maxlength="3" size="3" value="{$ipb[1]}">.
        <input type="text" class="ipgroup text short required" max="255" name="group3b" maxlength="3" size="3" value="{$ipb[2]}">.
        <input type="text" class="ipgroup text short required" max="255" name="group4b" maxlength="3" size="3" value="{$ipb[3]}">
      </p>

      <div class="button_container">
        <input type="reset" class="button mr_10" value="{$translations[$reset]}"/>
        <input type="submit" class="button red" value="{$translations[$save]}" />
      </div>

    </form>
  </div>

</fieldset>

