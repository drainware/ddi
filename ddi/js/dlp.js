/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function checkConcept(){
    $(".checkconcept").click(function(chkObj){
        var is_checked = chkObj["target"]['checked'];
        var value = chkObj["target"]['value'];
        var chk_elements = $("." + value).closest('input')
        for (var i=0;i<chk_elements.length;i++)
        {
            var chk_element = chk_elements[i];
            if (is_checked){
                chk_element['checked'] = true;
                chk_element['disabled'] = true;
            } else{
                chk_element['checked'] = false;
                chk_element['disabled'] = false;
            }
        }
    });
}

function changeAction(){
    $(".group_action").change(function() {
        $('#email_notification').hide();
        $(this).parent().parent().find('.group_action').each(function() {
            if(this.value == "alert"){
                $('#email_notification').show();
            }
        });
        
        
    });
}
