/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
    $( "#datepickerexpiry" ).datepicker({
        dateFormat: 'yy-mm-dd'
    });
    
    $('#wt-xu-find-client').click(function(){
        $('#wt-xu-users').val('0');
        $('#wt-xu-xusers').val('');
        $('#wt-xu-options').hide();
        if($('#wt-xu-lincese').val() != '' && !$('#wt-xu-lincese').val().match(/^\s*$/)){
            $.ajax({
                type: 'post',
                url: '?module=api&action=getClientUsers',
                data: {
                    "license" : $("#wt-xu-lincese").val(),
                    "password" : $("#wt-xu-password").val()
                }
            }).done(function(data){
                var response = jQuery.parseJSON(data);
                if(response.code == 1){
                    $('#company_name').text(response.company);
                    $('#wt-xu-users').val(response.users);
                    $('#wt-xu-xusers').val('0');
                    $('#wt-xu-options').show();
                } else if(response.code == 0){
                    dwShowNotification("withIcon", {
                        title: 'Find Customers', 
                        text: 'Customer not found in our database',
                        icon: 'images/notification_icon.png'
                    }, {
                        expires: 5000
                    });
                } else{
                    dwShowNotification("withIcon", {
                        title: 'Find Customer', 
                        text: 'Incorrect password',
                        icon: 'images/notification_icon.png'
                    }, {
                        expires: 5000
                    });
                }
            });
        } else {
            dwShowNotification("withIcon", {
                title: 'Find Customer', 
                text: 'Introduce a valid license',
                icon: 'images/notification_icon.png'
            }, {
                expires: 5000
            });
        }
    });
    
    $('#wt-xu-form').validate({
        messages: {
            license:"",
            nbr_users:"",
            extra_users:"",
            password:""
        }
    });
    
    $('#wt-xu-submit').click(function(){
        if($('#wt-xu-users').val() == '0'){
            dwShowNotification("withIcon", {
                title: 'Find Customer', 
                text: 'First, you should find a customer',
                icon: 'images/notification_icon.png'
            }, {
                expires: 5000
            });
        }
    });
    
});
