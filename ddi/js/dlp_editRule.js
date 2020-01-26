/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



$(document).ready(function() {
    $('#save-eform').click(function(){
        $('#edit_verify').val($('#edit_verify').val().trim());
        var code = $('#edit_verify').val();
        if(code != ""){
            var response = null;
            $.ajax({
                type: "POST",
                url: "?module=api&action=testPHPCode",
                data: {
                    code: code
                }
            }).done(function(data) {
                response = jQuery.parseJSON(data);
            }).fail(function(data){
                response = jQuery.parseJSON(data.responseText);
            }).always(function(){
                if(response.code == 1){
                    $('#save-eform').parent().parent().submit();
                } else {
                    dwShowNotification("default", {
                        title:'Verification Code', 
                        text:response.message
                    });
                }
            });
        } else{
            $('#save-eform').parent().parent().submit();
        }
    });
    
});