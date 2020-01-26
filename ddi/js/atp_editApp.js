/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
   
    $('#app_editable').change (function(){
        if ( this.checked ) {
            $('#app_variables input').removeAttr('readonly');
        } else {
            $('#app_variables input').attr('readonly','readonly');
            
        }
    });
   
   
});