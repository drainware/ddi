{ assign var="modify" value="Modify" }


<div id="header_bottom">

    {foreach from=$conf item=element}
      <p>
        <span class="entitle">{$element.display}:</span>

        {if $element.type eq 'string'}
          <input type="text" class="text required" name="{$element.property}" size="30" id="{$element.property}" value="{$element.value}" />
        {elseif $element.type eq 'boolean'}
          <label>On</label>
          <input {if $element.value eq 'checked' }checked="on"{/if} name="{$element.property}" value="on" type="radio" />
          <label for="">Off</label>
          <input {if $element.value neq 'checked' }checked="on"{/if} name="{$element.property}" value="off" type="radio" />
        {elseif $element.type eq 'numeric'}
          <input name="{$element.property}" size="4" value="{$element.value}" />
        {/if}

      </p>
    {/foreach}

  <input class="button" type="submit" value="{$translations[$modify]}">

</div>
