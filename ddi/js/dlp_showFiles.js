/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function loadColorbox(){
    $('.inline').colorbox({
        inline:true
    });
}

function configNewFiles(){
    if($('#file_list').children().length > 0){
        $('#launch_new_files').click();
    }
}


$(document).ready(function() {
    loadColorbox();
    configNewFiles()
    
    
    $(".file-element, .file-policies").mouseenter(function () {// show 
        var element_id = $(this).parent().parent().attr('id');
        $(".file-policies").fadeOut();
        $('#policies-' + element_id).fadeIn();
        $(".file-element").css({
            "z-index": "1110"
        });
    });
    
    
    $('.file-policy').click(function(){
        var form = $(this).parent().parent().parent().parent();
        $.ajax({
            type: 'GET',
            url: form.attr('action'),
            data: form.serialize(),
            success: function() {
                return false;
            }
        });
        
    });
    
    
});