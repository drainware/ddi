function dwShowNotification(message) {
 var id = Number(new Date());
 $("#notifications-area").append('<div id="' + id + '" class="system-dialog"><span>'+message+'</span><span class="close-dialog">x</span></div>');

 $('#'+id).delay(8000).fadeOut();  
 $('#'+id+' .close-dialog').click(function(){
   $(this).parent().fadeOut();
 });

}
