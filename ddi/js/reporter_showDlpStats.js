
var params = $('#query').val();

function getDlpEventsConsoleJson(){
    jQuery('#chart2').flexOptions({
        url: '?module=reporter&action=getConsoleDLP' + params
    }).flexReload();
}

function checkDLPElment(value){
    switch(value){
        case 0:
            $(".type_is").hide();
            $('input[name$="policies[]"]').removeAttr("checked");
            $('input[name$="concepts[]"]').removeAttr("checked");
            $('input[name$="subconcepts[]"]').removeAttr("checked");
            $('input[name$="rules[]"]').removeAttr("checked");
            $('input[name$="files[]"]').removeAttr("checked");
            $('input[name$="network_places[]"]').removeAttr("checked");
            $('input[name$="applications[]"]').removeAttr("checked");
            break;
        case 1:
            $(".type_is").hide();
            $('input[name$="concepts[]"]').removeAttr("checked");
            $('input[name$="subconcepts[]"]').removeAttr("checked");
            $('input[name$="rules[]"]').removeAttr("checked");
            $('input[name$="files[]"]').removeAttr("checked");
            $('input[name$="network_places[]"]').removeAttr("checked");
            $('input[name$="applications[]"]').removeAttr("checked");
            $("#type_is_policy").show();
            break;
        case 2:
            $(".type_is").hide();
            $('input[name$="policies[]"]').removeAttr("checked");
            $('input[name$="rules[]"]').removeAttr("checked");
            $('input[name$="files[]"]').removeAttr("checked");
            $('input[name$="network_places[]"]').removeAttr("checked");
            $('input[name$="applications[]"]').removeAttr("checked");
            $("#type_is_concept_subconcept").show();
            break;
        case 3:
            $(".type_is").hide();
            $('input[name$="policies[]"]').removeAttr("checked");
            $('input[name$="concepts[]"]').removeAttr("checked");
            $('input[name$="subconcepts[]"]').removeAttr("checked");
            $('input[name$="files[]"]').removeAttr("checked");
            $('input[name$="network_places[]"]').removeAttr("checked");
            $('input[name$="applications[]"]').removeAttr("checked");
            $("#type_is_rule").show();
            break;
        case 4:
            $(".type_is").hide();
            $('input[name$="policies[]"]').removeAttr("checked");
            $('input[name$="concepts[]"]').removeAttr("checked");
            $('input[name$="subconcepts[]"]').removeAttr("checked");
            $('input[name$="rules[]"]').removeAttr("checked");
            $('input[name$="network_places[]"]').removeAttr("checked");
            $('input[name$="applications[]"]').removeAttr("checked");
            $("#type_is_file").show();
            break;                
        case 5:
            $(".type_is").hide();
            $('input[name$="policies[]"]').removeAttr("checked");
            $('input[name$="concepts[]"]').removeAttr("checked");
            $('input[name$="subconcepts[]"]').removeAttr("checked");
            $('input[name$="rules[]"]').removeAttr("checked");
            $('input[name$="files[]"]').removeAttr("checked");
            $('input[name$="applications[]"]').removeAttr("checked");
            $("#type_is_network_place").show();
            break;
        case 6:
            $(".type_is").hide();
            $('input[name$="policies[]"]').removeAttr("checked");
            $('input[name$="concepts[]"]').removeAttr("checked");
            $('input[name$="subconcepts[]"]').removeAttr("checked");
            $('input[name$="rules[]"]').removeAttr("checked");
            $('input[name$="files[]"]').removeAttr("checked");
            $('input[name$="network_places[]"]').removeAttr("checked");
            $("#type_is_application").show();
            break;                    
    }
}

function getEventDetails(eurl){
    $.ajax({
        type: 'GET',
        url: eurl,
        success: function(data) {
            var event_object = jQuery.parseJSON(data);
        }
    });
}

$(document).ready(function(){

    $('#advance_query_clean').click(function(){
        $('#advance_query_clean').parent().hide();
        $('#advance_query').parent().show();
        $('#advance_query_clean').parent().removeClass('active');
    });

    $('#advance_query').click(function(){
        if($("#form-dlp-stats input:last-child").css("display")=="none"){
            $("#advance_query").css("background","url('images/triangle2.png') 0px 12px no-repeat");
        }
        else{
            $("#advance_query").css("background","url('images/triangle1.png') 0px 12px no-repeat");
        }
        $("fieldset fieldset").toggle("500","swing");
        $("#form-dlp-stats input:last-child").toggle("500","swing");
    });

    $( "#datepickerstart" ).datepicker({
        dateFormat: 'yy.mm.dd'
    });
    $( "#datepickerend" ).datepicker({
        dateFormat: 'yy.mm.dd'
    }); 

    $('#endpoint').click( function () {  
        if ($(this).is(':checked')){
            $('#endpoint_modules').show()
            $('.endpoint_module').attr("checked", "checked");
        }else {
            $('#endpoint_modules').hide()
            $('.endpoint_module').removeAttr("checked");
        }
    });
    
    if ($('.endpoint_module').is(':checked')){
        $('#endpoint_modules').show()
    }else {
        $('#endpoint_modules').hide()
    }
    
    $('.endpoint_module').click( function () {  
        if (!$(this).is(':checked')){
            $('#endpoint').removeAttr("checked")
        }
    });
    
    $('#multiple_groups').click(function(){
        if ($(this).is(':checked')){
            $('#group_field').attr('multiple', 'multiple')
        }else {
            $('#group_field').removeAttr('multiple')
        }
    });

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
    
    
    $('.labelconcept:parent').click( function () {
        var span_concept_id = ".label_" + $(this).attr('id');
        if ($(span_concept_id).attr('style')){
            $(span_concept_id).removeAttr("style")
            $(this).parent().css("background","url('images/triangle2.png') left no-repeat");

        }else {
            $(span_concept_id).attr("style", "display:none");
            $(this).parent().css("background","url('images/triangle1.png') left no-repeat");
        }
    });

    
    $.getJSON('?module=reporter&action=getDlpEventsHistogram' + params, function(data) {
        if (data != ""){
            $("#chart1").empty();
            
            var histdata = data;
            $.jqplot('chart1', [histdata], {
                title: t_dlp_events,
                axes:{
                    xaxis:{
                        renderer:$.jqplot.DateAxisRenderer,
                        tickOptions:{
                            formatString:'%b&nbsp;%#d'
                        }
                    },
                    yaxis:{

                }
                },
                highlighter: {
                    show: true,
                    sizeAdjust: 10
                },
                cursor: {
                    show: true
                }
            });
        }
    });
    
    $("#chart2").flexigrid({
        url: '?module=reporter&action=getConsoleDLP' + params,
        dataType: 'json',
        title: t_message_dlp_event_detail,
        colModel : [

        {
            display: 'Date', 
            name : 'timetime', 
            width : 100, 
            sortable : false, 
            align: 'left'
        },

        {
            display: 'Username', 
            name : 'user', 
            width : 60, 
            sortable : false, 
            align: 'left',
            hide:false
        },

        {
            display: 'Origin', 
            name : 'origin', 
            width : 45, 
            sortable : false, 
            align: 'center',
            hide:true
        },
        
        {
            display: 'Type', 
            name : 'type', 
            width :100, 
            sortable : false, 
            align: 'left',
            hide:false
        },

        {
            display: 'Identifier', 
            name : 'identifier', 
            width :110, 
            sortable : false, 
            align: 'left'
        },
        
        {
            display: 'Action', 
            name : 'action', 
            width : 50, 
            sortable : false, 
            align: 'center',
            hide:false
        },

        {
            display: 'Severity', 
            name : 'severity', 
            width :60, 
            sortable : false, 
            align: 'center',
            hide:false
        },

        ],
        usepager: true,
        useRp: false,
        rp: 15,
        resizable: false,
        singleSelect: true
        
    });

    $(function() { 
        $('#chart2').click( function (myObj) {
            var content = myObj['target']['parentElement']['parentElement']['id'];
            content = content.replace(/row/g, "");
            var mongoid_re = /^[a-fA-F0-9]+$/;
            if (content.match(mongoid_re)){
                var mongo_id = content;
                var redirect_url = "?module=reporter&action=consoleDlpEvent&event_id=" + mongo_id;
                /*
                $.colorbox({
                    width:"80%", 
                    height:"80%", 
                    iframe:true, 
                    href:redirect_url
                });
                */
                window.location = redirect_url;
            }
        }); 
    });
    
    $('#whom').change(function() {
        var value = parseInt($(this).val());
        switch(value){
            case 0:
                $(".whom_by").hide();
                $("#ip_field_1").val("");
                $("#ip_field_2").val("");
                $("#ip_field_3").val("");
                $("#ip_field_4").val("");
                $(".group_field_opt").removeAttr("selected");
                $("#user_field").val("");
                break;
            case 1:
                $(".whom_by").hide();
                $(".group_field_opt").removeAttr("selected");
                $("#user_field").val("");
                $("#whom_by_ip").show()
                break;
            case 2:
                $(".whom_by").hide();
                $("#ip_field_1").val("");
                $("#ip_field_2").val("");
                $("#ip_field_3").val("");
                $("#ip_field_4").val("");
                $("#user_field").val("");
                $("#whom_by_group").show();
                break;
            case 3:
                $(".whom_by").hide();
                $("#ip_field_1").val("");
                $("#ip_field_2").val("");
                $("#ip_field_3").val("");
                $("#ip_field_4").val("");  
                $(".group_field_opt").removeAttr("selected");
                $("#whom_by_user").show()
                break;
            default:
                alert("error");
        }
    });

    var whom_value = parseInt($('#whom').val());
    switch(whom_value) {
        case 0:
            $(".whom_by").hide();
            $("#ip_field_1").val("");
            $("#ip_field_2").val("");
            $("#ip_field_3").val("");
            $("#ip_field_4").val("");
            $(".group_field_opt").removeAttr("selected");
            $("#user_field").val("");
            break;
        case 1:
            $(".whom_by").hide();
            $(".group_field_opt").removeAttr("selected");
            $("#user_field").val("");
            $("#whom_by_ip").show()
            break;
        case 2:
            $(".whom_by").hide();
            $("#ip_field_1").val("");
            $("#ip_field_2").val("");
            $("#ip_field_3").val("");
            $("#ip_field_4").val("");
            $("#user_field").val("");
            $("#whom_by_group").show();
            break;
        case 3:
            $(".whom_by").hide();
            $("#ip_field_1").val("");
            $("#ip_field_2").val("");
            $("#ip_field_3").val("");
            $("#ip_field_4").val("");  
            $(".group_field_opt").removeAttr("selected");
            $("#whom_by_user").show()
            break;
        default:
            alert("error");
    } 
    
    $('#type').change(function() {
        var value = parseInt($(this).val());
        checkDLPElment(value);
    });
    
    var value = parseInt($('#type').val());
    checkDLPElment(value);
    
});