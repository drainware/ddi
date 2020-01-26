   
function remoteExplorer(){
    
    $("#file_explorer_machine").addClass('error');
    $("#file_explorer_ip").addClass('error');
    
    if ($("#file_explorer_machine").val() != "" && !$("#file_explorer_machine").val().match(/^\s*$/)){
        $("#file_explorer_machine").removeClass('error');
    } 
    
    if ($("#file_explorer_ip").val() != "" && !$("#file_explorer_machine").val().match(/^\s*$/)){
        $("#file_explorer_ip").removeClass('error');
    }
    
    if (!$("#file_explorer_machine").hasClass('error')){
        if (!$("#file_explorer_ip").hasClass('error')){
            
            $("#file_explorer_machine").val($("#file_explorer_machine").val().trim());
            $("#file_explorer_ip").val($("#file_explorer_ip").val().trim());
            
            var channel = $("#file_explorer_machine").val() + '_' + $("#file_explorer_ip").val();
    
            $("#file_explorer_channel").val(channel);

            if ($("#file_explorer_path").val() != ""){
                $("#file_explorer_command").val('list');
                if ($("#file_explorer_path").val().slice(-1) != '\\' ){
                    $("#file_explorer_path").val($("#file_explorer_path").val() + '\\')
                } 
            } else {
                $("#file_explorer_command").val('listUnits');
            }
    
            $("#remote_file_explore_results").show();
            
            $("#file_explorer_list").empty();
            $("#loading_search").show();
            
            $.ajax({
                type: 'POST',
                url: $("#file_explorer_form").attr('action'),
                data: $("#file_explorer_form").serialize(),
                success: function(data) {
                    var data_object = jQuery.parseJSON(data);
                    $("#file_explorer_id").val(data_object.id);
                    if ($("#file_explorer_command").val() != "list"){
                        getLastListUnitsResult();
                        idinterval = setInterval("getLastListUnitsResult()", 2000);
                    } else {
                        getLastListResult();
                        idinterval = setInterval("getLastListResult()",2000);
                    }
            
                }
            });
        } else {
            $("#remote_file_explore_results").hide();
            $("#file_explorer_list").empty();
        }
    } else{
        $("#remote_file_explore_results").hide();
        $("#file_explorer_list").empty();  
    }
    
    
    
}

function getLastListUnitsResult(){
    $.ajax({
        type: 'POST',
        url: '?module=forensics&action=getLastListResult',
        data: {
            id : $("#file_explorer_id").val()
        },    
        success: function(data) {
            
            if (data != "null"){
                clearInterval(idinterval);
            

                var units = jQuery.parseJSON(data);
        
                $("#loading_search").hide();
                $("#file_explorer_list").empty();
        
                $.each(units.payload.regular, function() {
        
                    var unit = '<a class="remote_unit">' + this + ':</a><br />';
                    $("#file_explorer_list").append(unit);
                });
            
                $.each(units.payload.removable, function() {
        
                    var unit = '<a class="remote_unit">' + this + ':</a><br />';
                    $("#file_explorer_list").append(unit);
                });        

            }
        }
    });
}

function getLastListResult(){
    $.ajax({
        type: 'POST',
        url: '?module=forensics&action=getLastListResult',
        data: {
            id: $("#file_explorer_id").val()
        },    
        success: function(data) {
            
            if (data != "null"){
                clearInterval(idinterval);
                
                var list = jQuery.parseJSON(data);
                
                $("#loading_search").hide();
                if(list.payload != null){
                    $.each(list.payload.directories, function() {
                        if(this != '.'){
                            var unit = '<a class="remote_directory">' + this + '</a><br />';
                            $("#file_explorer_list").append(unit);
                        }
                    });
            
                    $.each(list.payload.files, function() {
                        var unit = null;
                        if(account_type == 'premium'){
                            if(this.size > (50 * 1024 * 1024)){
                                unit = '<a class="download-warning">' + this.name + '</a><br />';
                            } else{
                                unit = '<a class="remote_file">' + this.name + '</a><br />';
                            }
                        } else{
                            if(this.size > (50 * 1024 * 1024)){
                                unit = '<a class="download-warning">' + this.name + '</a><br />';
                            } else if(this.size > (5 * 1024 * 1024)){
                                unit = '<a class="upgrade-premium">' + this.name + '</a><br />';
                            } else{
                                unit = '<a class="remote_file">' + this.name + '</a><br />';
                            }
                        }
                        $("#file_explorer_list").append(unit);
                    });                    
                } else {
                    var unit = '<p> No results were found </p><br/><a class="no_results button">back</a><br/>';
                    $("#file_explorer_list").append(unit);
                }

            }

        }
    });
}


function getLastGetResult(){
    $.ajax({
        type: 'POST',
        url: '?module=forensics&action=getLastGetResult',
        data: {
            id: $("#file_explorer_id").val()
        },    
        success: function(data) {
            if (data == "success"){
                clearInterval(idinterval);
                window.location = "?module=forensics&action=downloadRemoteFile&id=" + $("#file_explorer_id").val();
            }
        }
    });
}

function listCommand(dir_name){
   
    if (dir_name == '..'){
        var tmp_path = $("#file_explorer_path").val().substring(0, $("#file_explorer_path").val().length - 1);
        tmp_path = tmp_path.substring(0, tmp_path.lastIndexOf('\\'));
        $("#file_explorer_path").val(tmp_path + '\\');
    } else {
        $("#file_explorer_path").val($("#file_explorer_path").val() + dir_name + '\\');
    }
    
    if($('#file_explorer_path').val() != "\\"){
        $("#file_explorer_command").val("list");
    
        $("#file_explorer_list").empty();
        $("#loading_search").show();

        $.ajax({
            type: 'POST',
            url: $("#file_explorer_form").attr('action'),
            data: $("#file_explorer_form").serialize(),
            success: function(data) {
                var data_object = jQuery.parseJSON(data);
                $("#file_explorer_id").val(data_object.id);
                getLastListResult();
                idinterval = setInterval("getLastListResult()",2000);
            }
        }); 
    } else {        
        $('#file_explorer_path').val("");
        remoteExplorer();
    }
             
}

function getCommand(object_clicked){
    clearInterval(idinterval);
    $("#file_explorer_command").val('get');

    $.ajax({
        type: 'POST',
        url: $("#file_explorer_form").attr('action'),
        data: {
            channel: $("#file_explorer_channel").val(),
            command: $("#file_explorer_command").val(),
            args: $("#file_explorer_path").val() + object_clicked.text()
        },    
        success: function(data) {
            var data_object = jQuery.parseJSON(data);
            $("#file_explorer_id").val(data_object.id);
            getLastGetResult();
            idinterval = setInterval("getLastGetResult()",2000);
        }
    });      
}

$(document).ready(function(){
    idinterval = 0;
    $('#file_explorer_button').click(function(){
        clearInterval(idinterval);
        remoteExplorer();
    });
    
    
    $(document).on("click", ".remote_unit", function(){
        listCommand($(this).text());
    });
    
    $(document).on("click", ".remote_directory", function(){
        listCommand($(this).text());
    });
    
    $(document).on("click", ".remote_file", function(){
        getCommand($(this));
    });
    
    $(document).on("click", ".no_results", function(){
        listCommand('..');
    });
    
    $(document).on("click", ".download-warning", function(){
        dwShowNotification("default", {
            title:'Download ' + this.text, 
            text:'You can download files up to 50MB of weigth'
        });
    });
    
    $(document).on("click", ".upgrade-premium", function(){
        dwShowNotification("default", {
            title:'Download ' + this.text,
            text:'Upgrade your plan to premium <a href="?module=main&action=showCloudConfig">here</a> to download files up to 50MB of weigth'
        });
    });
    
    if ($("#file_explorer_machine").val() != "" && !$("#file_explorer_machine").val().match(/^\s*$/)){
        if ($("#file_explorer_ip").val() != "" && !$("#file_explorer_machine").val().match(/^\s*$/)){
            $('#file_explorer_button').click();
        }
    }
   
});





