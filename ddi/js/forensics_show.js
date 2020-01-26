/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$("#remote_search").click(function() {
    nro_results = 0;
    if ($("#remote_search_args").val() != "" && !$("#remote_search_args").val().match(/^\s*$/)){
        var search = true;

        $("#remote_search_args").removeClass('error');
        $("#remote_search_args").val($("#remote_search_args").val().trim());
        
        if($("#remote_search_args").val().length < 4){
            if (!confirm('The search term is too short and can take a lot of time, are you sure you want to continue?', 'Remote Search Alert')){
                search = false;
            }
        }

        if(search){
            $.ajax({
                type: 'POST',
                url: $("#remote_search_form").attr('action'),
                data: $("#remote_search_form").serialize(),
                success: function(data) {
                    var search_object = jQuery.parseJSON(data);
                    $("#remote_search_id").val(search_object.id);
                    $("#results_search").show();
                    $("#results").empty();
                    $("#info_messages").hide();
                    $("#loading_search").show();
                    $("#nro_results").html("Results (0)");
                    $("#remote_search").hide();
                    $("#cancel_search").show();
                    getLastResults();
                    idinterval_remote_search = setInterval("getLastResults()",2500);
                }
            });
        }
    } else {
        $("#remote_search_args").addClass('error');
        $("#results").empty();
        $("#results_search").hide();
    }
    return false;
});

$("#cancel_search").click(function() {
    clearInterval(idinterval_remote_search);
    $("#cancel_search").hide();
    $("#remote_search").show();
    $("#loading_search").hide();
    $("#info_messages").show();
    $("#info_messages").html("The search has been cancelled");
    return false;
});

$("#file_type_filter").change(function() {
    $("#remote_search").click();
})

function getLastResults(){
    Concurrent.Thread.create(function(){
        $.ajax({
            type: 'POST',
            url: '?module=forensics&action=getLastSearchResults',
            data: {
                id: $("#remote_search_id").val(), 
                last_id: $("#remote_search_last_id").val()
            },
            success: function(data) {
                var results_search_object = jQuery.parseJSON(data);
                showResults(results_search_object);
            }
        });
    });
}

function showResults(results_search_object){
    $.each(results_search_object , function(){
        var result_search_object = this;
        Concurrent.Thread.create(generateDeviceResult, result_search_object);
    });
    
}

function requestRemoteFile(object_form){
    clearInterval(idinterval_remote_file);
    $.ajax({
        type: 'POST',
        url: object_form.attr('action'),
        data: object_form.serialize(),   
        success: function(data) {
            var data_object = jQuery.parseJSON(data);
            remote_file_id = data_object.id;            
            downloadRemoteFile();
            idinterval_remote_file = setInterval("downloadRemoteFile()",2000);
        }
    });      
}

function downloadRemoteFile(){
    $.ajax({
        type: 'POST',
        url: '?module=forensics&action=getLastGetResult',
        data: {
            id: remote_file_id
        },    
        success: function(data) {
            if (data == "success"){
                clearInterval(idinterval_remote_file);
                window.location = "?module=forensics&action=downloadRemoteFile&id=" + remote_file_id;
            }
        }
    });
}

function reloadColorbox(){
    $('.inline').colorbox({
        inline:true, 
        width:"50%"
    });
}


function generateDeviceResult(result_search_object){

    $("#remote_search_last_id").val(result_search_object._id.$id);
    
    if(result_search_object.payload != null){
        
        var device_id = result_search_object.device + '_' + result_search_object.ip.toString().replace(/\./g, '-');
        
        if (!$('#' + device_id).length) {
            var args = '&device=' + result_search_object.device + '&ip=' + result_search_object.ip;
            var file_explorer_link = '<a href="/ddi/?module=forensics&action=showRemoteFileExplorer' + args + '" target="_blank"> <img src="images/explorer.png" style="margin-right: 10px; margin-bottom: -5px;" />' + result_search_object.device + ' - ' + result_search_object.ip + '</a>';
            var result_fieldset = '' +
            '<fieldset id="' + device_id + '" class="results_accordion" >' +
                '<legend>' + file_explorer_link + '<div style="float: right; padding-right: 10px;padding-top: 2px;"><u><b>0</b> coincidences</u></div></legend>' +
            '</fieldset>';
            
            $("#loading_search").hide();
            $("#results").append(result_fieldset)       
            
            nro_results = nro_results + 1;
            $("#nro_results").html("Results (" + nro_results.toString() + ")");
        }
            
        var span_user = '<span style="display:none"><h2>' + result_search_object.user + '</h2></span>';
        var span_list = '<span id="list_' + result_search_object._id.$id + '" style="display:none"></span>';

        $("#" + device_id).append(span_user);
        $("#" + device_id).append(span_list);        
        $('#' + device_id + ' legend u b').html(parseInt($('#' + device_id + ' legend u b').html()) + result_search_object.payload.length)

        $.each(result_search_object.payload , function(){
            var result_object = this;
            Concurrent.Thread.create(generateFileParagraph,device_id, result_search_object, result_object )
        });
        
    }
    
}

function generateFileParagraph(device_id, result_search_object, result_object){
                  
    var result_p = '' + 
    '<p style="display:block">' + 
        '<a class="inline cboxElement" href="#' + result_object.file_id + '">' +  result_object.name + '</a>' +
    '</p>';

    var download_button = '<input type="button" name="'+ result_object.name + '" value="download" class="' + getDownloadClass(result_object.size) + ' button" /> ';
    if(result_object.itype == 'Directory'){
        download_button = '';
        result_object.type = result_object.itype;
    }
    
    if(result_object.context == 'BAD STATUS'){
        result_object.context = 'Not available';
    }
    
    if(result_object.type == 'BAD STATUS'){
        result_object.type = 'Not available';
    }
    
    var result_form = '' + 
    '<form method="post" action="?module=forensics&action=remoteQuery">' +
        '<input type="hidden" name="id" value="" id="file_explorer_id" />' +
        '<input type="hidden" name="channel" value="' + result_search_object.device + '_' + result_search_object.ip + '" id="file_explorer_channel" />' +
        '<input type="hidden" name="command" value="get" id="file_explorer_command" />' +
        '<input type="hidden" name="args" value="' + result_object.url + '" id="file_explorer_path" /> ' +
        download_button +
    '</form>'

    var result_div = '' +
    '<div style="display:none">' + 
        '<div id="' + result_object.file_id + '" style="padding:10px; background:#fff;">' + 
            '<h1>Remote File View</h1>' +
            '<table id="hor-zebra"  style="width: 90%">' + 
                '<tbody>' + 
                    '<tr>' + 
                        '<td rowspan="1" colspan="2"> <strong>Date</strong> </td>' + 
                        '<td rowspan="1" colspan="2"> ' + result_search_object.datetime + ' </td>' + 
                    '</tr>' + 
                    '<tr class="odd">' + 
                        '<td> <strong>Machine</strong> </td> <td> ' + result_search_object.device + ' </td>' + 
                        '<td> <strong>IP</strong> </td> <td> ' + result_search_object.ip + ' </td>' + 
                    '</tr>' + 
                    '<tr>' + 
                        '<td> <strong>Path</strong> </td> <td> ' + result_object.path + ' </td>' + 
                        '<td> <strong>Name</strong> </td> <td> ' + result_object.name + ' </td>' + 
                    '</tr>' + 
                    '<tr class="odd">' + 
                        '<td> <strong>Modified</strong> </td> <td> ' + result_object.modified + ' </td>' + 
                        '<td> <strong>Type</strong> </td> <td> ' + result_object.type + ' </td>' + 
                    '</tr>' +   
                    '<tr>' + 
                        '<td rowspan="1" colspan="4"> ' + result_object.context + ' </td>' + 
                    '</tr>' +                                     
                '</tbody>' + 
            '</table>' + 
        '</div>' +
    '</div>';
                
    $("#list_" + result_search_object._id.$id).append(result_p);
    $("#" + device_id).append(result_div);
    $("#" + result_object.file_id).append(result_form);

    reloadColorbox();

}

function getDownloadClass(size){
    var s_class = 'remote_file';
    if(account_type == 'premium'){
        if(size > (50 * 1024 * 1024)){
            s_class = 'download-warning';
        }
    } else{
        if(size > (50 * 1024 * 1024)){
            s_class = 'download-warning';
        } else if(size > (5 * 1024 * 1024)){
            s_class = 'upgrade-premium';
        }
    }
    return s_class;
}

$(document).on("click", ".results_accordion legend div", function(){ 
    var parent = $(this).parent().parent();
    if( parent.hasClass('active') ){
        parent.removeClass('active').find('span').hide();
        $(this).css("background","url('images/triangle1.png') left no-repeat");
    }else{
        parent.addClass('active').find('span').show();
        $(this).css("background","url('images/triangle2.png') left no-repeat");
    }
}); 

$(document).on("click", ".remote_file", function(){ 
    idinterval_remote_file = 0;
    requestRemoteFile($(this).parent());        
});

$(document).on("click", ".download-warning", function(){
    dwShowNotification("default", {
        title:'Download ' + this.name, 
        text:'You can download files up to 50MB of weigth'
    });
});
    
$(document).on("click", ".upgrade-premium", function(){
    dwShowNotification("default", {
        title:'Download ' + this.name,
        text:'Upgrade your plan to premium <a href="?module=main&action=showCloudConfig">here</a> to download files up to 50MB of weigth'
    });
});