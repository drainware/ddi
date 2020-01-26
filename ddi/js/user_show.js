/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).ready(function(){
    $('.local').multiselect({
        selectedText: "# group(s) selected"
    });
    
    $('.local').change(function(){
        var uid = this.id;
       
        var gids = $('#' + uid).multiselect('getChecked').map(function(){
            return this.value;    
        }).get();
       
        $.ajax({
            type: "POST",
            url: "?module=user&action=modify",
            data: {
                id: uid, 
                groups: gids
            }
        }).done(function() {
        });
    });
    
    $('.ldap').multiselect({
        header: false,
        selectedText: function(numChecked, numTotal, checkedItems){
            var selected_text = numChecked + ' groups selected';
            if(numChecked < 2){
                selected_text = numChecked + ' group selected';
            }
            return selected_text;
        }
    });
    
    $('.ldap').on('multiselectclick', function(event, ui) { 
        /* event: the original event object 
         * ui.value: value of the checkbox 
         * ui.text: text of the checkbox 
         * ui.checked: whether or not the input was checked or unchecked (boolean) 
         */ 
        return false;
    });
    
});