
cost_user_month = 6.99;

nbr_users = parseInt($("#client_nbr_users").val());
extra_users = 0;
total_amount = 0;


$("#client_type_premium").click(function() {
    $("#preemium_client_opt").show();
    $("#total_amount_block").show();
    $("#button_freemium_cloud_user").hide();
    $("#button_premium_cloud_user").show();
    $("#client_period_1m").click();
});

$("#client_type_freemium").click(function() {
    $("#preemium_client_opt").hide();
    $("#button_freemium_cloud_user").show();
    $("#button_premium_cloud_user").hide();
});

$("#client_nbr_users").bind('keydown keypress', function(event) {
    setTimeout(function() {
        nbr_users = 0;
        if($("#client_nbr_users").val() != ""){
            nbr_users = parseInt($("#client_nbr_users").val());
        }
        if(nbr_users >= 250){
            dwShowNotification("withIcon", {
                title:'Advice', 
                text: 'For large numbers of users (more than 250), please, send us an email to sales@drainware.com and we will make you special discounts.',
                icon: 'images/notification_icon.png'
            }, {
                expires: 5000
            });
            $('#total_amount_block').hide();
            $('#button_premium_cloud_user').hide();
        } else{
            $('#total_amount_block').show();
            $('#button_premium_cloud_user').show();
        }
        calculate_final_cost(nbr_users);
    }, 0);
});

$("#client_period_1m").click(function() {
    //$("#subscription_payment_form").show();
    //$("#payment_form").hide();
    $("#client_period_xm").val('1 month(s)');
    $("#percent_discount").val('0%');
    $("#payment_pack_name").val('Drainware Premium Pack 1 month');
    calculate_final_cost(nbr_users);
});

$("#client_period_12m").click(function() {
    //$("#subscription_payment_form").hide();
    //$("#payment_form").show();
    $("#client_period_xm").val('12 month(s)');
    $("#percent_discount").val('10%');
    $("#payment_pack_name").val('Drainware Premium Pack 1 year');
    calculate_final_cost(nbr_users);
});

$("#client_period_24m").click(function() {
    //$("#subscription_payment_form").hide();
    //$("#payment_form").show();
    $("#client_period_xm").val('24 month(s)');
    $("#percent_discount").val('20%');
    $("#payment_pack_name").val('Drainware Premium Pack 2 year');
    calculate_final_cost(nbr_users);
});

$("#client_period_36m").click(function() {
    //$("#subscription_payment_form").hide();
    //$("#payment_form").show();
    $("#client_period_xm").val('36 month(s)');
    $("#percent_discount").val('30%'); 
    $("#payment_pack_name").val('Drainware Premium Pack 3 year');
    calculate_final_cost(nbr_users);
});

$("#button_premium_cloud_user").click(function() {
    if($("#client_type_premium").attr("checked") == "checked"){
        if(!isNaN(nbr_users)){
            if(nbr_users > 0){
                $.ajax({
                    async: false,
                    type: 'POST',
                    url: '?module=cloud&action=savePremiumUpgrade',
                    data: {
                        "license" : $("#client_license").val(),
                        "company" : $("#client_company").val(),
                        "nbr_users" : nbr_users,
                        "extra_users" : extra_users,
                        "period" : parseInt($("#client_period_xm").val()),
                        "cost_user_per_month" : $("#client_cost_user_month").val(),
                        "total_amount" : final_amount 
                    }
                }).done(function(data){
                    if(data != '0'){
                        $("#payment_id").val(data);
                        $("#payment_amount").val(final_amount);
                        $("#subscription_payment_id").val(data);
                        $("#subscription_payment_amount").val(final_amount);
                        
                        $("#payment_form").submit();
                            
                        setInterval(function checkPayment(){
                            $.ajax({
                                type: "POST",
                                url: "?module=cloud&action=checkPayment",
                                data: {
                                    payment_id: data 
                                }
                            }).done(function(data) {
                                if(parseInt(data) == 0){
                                    location.reload()
                                }
                            }).always(function() {
                                });
                        },2500);
                    } else{
                        dwShowNotification("sticky", { title:'Error', text:'We have detected suspicious activity, a message has been sent to the fraud team'}, {expires:false});
                    }
                });
            }else{
                dwShowNotification("default", { title:'Error', text:'Nbr of users not valid'});
            }
        } else {
            dwShowNotification("default", { title:'Error', text:'Nbr of users is not a number'});
        }
    }
});

$(document).ready(function(){
    
    $('#total_amount_block').hide();
    if($('#client_period_1m').is(':checked')){
        $('#client_period_1m').click();
    }else if ($('#client_period_12m').is(':checked')){
        $('#client_period_12m').click();
    }else if ($('#client_period_24m').is(':checked')){
        $('#client_period_24m').click();
    }else if ($('#client_period_36m').is(':checked')){
        $('#client_period_36m').click();
    }
    
    if ($('input[name="period"]:checked').val() == null && $("#client_period_xm").val() != ' month(s)'){
        $('#xperiod').show();
        $('#periods').hide();
    }
    
    $('#reset_extra_users').click(function(){
        $('#xperiod').hide();
        $('#periods').show();
        if ($('input[name="period"]:checked').val() == null){
            $('#xperiod').show();
            $('#periods').hide();
        }
        $('#total_amount_block').hide();
    });
    
    
    $('#add_extra_users').click(function(){
        
        if ($("#extra_users").val() != "" & !isNaN(parseInt($("#extra_users").val()))){
            
            /*
            if($('#client_period_1m').is(':checked') & (extra_users == 0)){
                dwShowNotification("withIcon", {
                        title:'Warning', 
                        text: 'Before add users, you should cancel the current subscription. If you have any questions, please mail us at sales@drainware.com',
                        icon: 'images/notification_alert.png'
                }, {
                    expires:false
                });
            }
            */
           
            extra_users = parseInt($("#extra_users").val());
            
            calculate_months();
            calculate_final_cost(extra_users);
            
            $('#button_premium_cloud_user').show();
            
            if((nbr_users + extra_users) >= 250){
                
                dwShowNotification("withIcon", {
                    title:'Advice', 
                    text: 'For large numbers of users (more than 250), please, send us an email to sales@drainware.com and we will make you special discounts.',
                    icon: 'images/notification_icon.png'
                }, {
                    expires: 5000
                });
                    
                $('#period_block').hide();
                $('#total_amount_block').hide();
                $('#button_premium_cloud_user').hide();
            } else{
                $('#period_block').show();
                $('#total_amount_block').show();
                $('#button_premium_cloud_user').show();
            }
            
        }else{
            dwShowNotification("default", {
                    title:'Error', 
                    text:'Insert a valid number of extra users'
            });
        }
    });
    
});

function calculate_months(){
    var now = new Date();
    var expiry = new Date($('#client_expiry').val());
    var months = Math.round(Math.abs(expiry - now) / (1000 * 3600 * 24 * 30));
    $("#client_period_xm").val(months + ' month(s)');
    $('#xperiod').show();
    $('#periods').hide();
    $("#percent_discount").val('0%');
}

function calculate_final_cost(users){
    if(!isNaN(users)){
        if((nbr_users + extra_users) < 50){
            cost_user_month = 6.99;
        } else if((nbr_users + extra_users) <= 100){
            cost_user_month = 5.99;
        } else {
            cost_user_month = 4.99;
        }
        
        var nmonths = parseInt($("#client_period_xm").val());
        var period_discount = parseInt($("#percent_discount").val()) / 100;
        
        total_amount = cost_user_month * users * nmonths;
        final_amount = total_amount * (1 - period_discount);
        total_amount = total_amount.toFixed(2);
        final_amount = final_amount.toFixed(2);
    } else {
        dwShowNotification("default", { title:'Error', text:'Nbr of users is not a number'});
        $("#client_cost_user_month").val("0.0");
        $("#total_amount").val("6.99");
        $("#final_amount").val("0.0");
    }
    $("#client_cost_user_month").val(cost_user_month);
    $("#total_amount").val(total_amount);
    $("#final_amount").val(final_amount);
}

