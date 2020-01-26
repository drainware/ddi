{ assign var="submit" value="Save" }
{ assign var="t_white_list" value="White List" }
{ assign var="t_black_list" value="Black List" }

  <h2>{$translations[$lists]}</h2>
  <div class="module_content">
    <form action ="?module=webfilter&action=modify" method="POST">
	  
      <fieldset><legend>{$translations[$lists]}</legend>
		<input type="hidden" name="name" value="{$group_name}">
        <table width="100%" cellpadding="0" cellspacing="5">
          <tr>
            <td>
              {$translations[$t_white_list]}:<br />
              <textarea class="textarea_lists" id="white_list" cols=38 rows=25 name="white_list">{$white_list}</textarea>
            </td>
            <td>
              {$translations[$t_black_list]}:<br />
              <textarea class="textarea_lists" id="black_list" cols=38 rows=25 name="black_list">{$black_list}</textarea>
            </td>
          </tr>
        </table>
      </fieldset> 
      <input class="button" type="submit" value="{$translations[$submit]}" />

    </form>
  </div>
