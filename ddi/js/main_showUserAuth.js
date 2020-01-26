
$("#user_auth_ldap").click(function() {
    enabledLDAP();
});

$("#user_auth_local").click(function() {
    enabledLDAP();
});

$(document).ready(function(){
    enabledLDAP();
    
    $("#save-conf").click(function() {
        if ($('#user_auth_ldap').is(':checked')) {
            if (testLDAPConnection() == 0){
                    if($("#list-groups").find('p').length != 0){
                        $.colorbox({
                            inline:true, 
                            href:"#local-groups",
                            onClosed: function () {
                                $("#import-submit").click();
                            }
                        });
                    } else{
                        this.form.submit(); 
                    }
                }
        } else{
            this.form.submit(); 
        }
    });
   
   
   $("#import-submit").click(function() {
        $.ajax({
            type: 'POST',
            url: $("#import-local-ldap").attr('action'),
            data: $("#import-local-ldap").serialize(),
            success: function() {
                $("#save-conf").parent().submit();
            }
        });
    });
    
    $("#test-conf").click(function(){
        testLDAPConnection(true);
    });
    
});

function enabledLDAP(){
    if ($('#user_auth_local').is(':checked')) {
        $('#ldap_config :input').attr('disabled', 'disabled');
        $('#test-conf').hide();
        $('#ldap_config').hide();
    } else {
        $('#ldap_config :input').removeAttr('disabled');
        $('#test-conf').show();
        $('#ldap_config').show();
    }
}

function testLDAPConnection(test_only){
    var code = null;
    $.ajax({
        async: false,
        type: "POST",
        url: "?module=main&action=testLDAPConnection",
        data: {
            version: $('#ldap_conf_version').val(),
            host: $('#ldap_conf_host').val(),
            port: $('#ldap_conf_port').val(),
            dn: $('#ldap_conf_dn').val(),
            password: $('#ldap_conf_password').val(),
            base: $('#ldap_conf_base').val()
        }
    }).done(function(data) {
        code = parseInt(data);
        
        switch(code){
            case 0:
                if(test_only){
                    dwShowNotification("default", { title:'Test LDAP Connection',text:'Succes'});    
                }
                break;
            case -1:
                dwShowNotification("default", { title:'Test LDAP Connection', text:'Can not contact LDAP server or Invalid credentials'});
                break;
            case -2:
                dwShowNotification("default", { title:'Test LDAP Connection', text:'Could not find any group with the current Base'});
                break;   
            default:
                dwShowNotification("default", { title:'Test LDAP Connection', text:'Unknow Error'});
                break;
        }
        
    }).always(function() {
    });
    return code;
}