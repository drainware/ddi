
$(document).ready(function(){
    enabledNotification();
    
    $("#notification_enabled").click(function() {
        enabledNotification();
    });

    $("#notification_disabled").click(function() {
        enabledNotification();
    });
    
    $("#browser_permission").click(function(){
        Notification.requestPermission(function(perm){
           if(perm == notify.PERMISSION_GRANTED){
               $("#browser_permission").hide();
               dwShowDesktopNotfication('Drainware', 'Notification service enabled')
               getLastEventNotifications();
           }
       })
    });
});

function enabledNotification(){
    if ($('#notification_enabled').is(':checked')) {
        $('#notify_when').show();
        checkBrowserPermission();
    } else {
        $('#notify_when').hide();
        $("#browser_permission").hide();
    }
}

function checkBrowserPermission(){
     if(notify.isSupported){
         $("#browser_permission").show();
         if(notify.permissionLevel() != notify.PERMISSION_DEFAULT ){
             $("#browser_permission").hide();
         }
     } else{
         $("#browser_permission").hide();
         dwShowNotification("withIcon", {
            title: 'Warning!', 
            text: 'This browser not support desktop notifications', 
            icon: 'images/notification_alert.png'
        },{ 
            expires:false
        });
     }
}