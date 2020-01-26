
var params = $('#query').val();

function getDlpEventsConsoleJson(){
    jQuery('#chart2').flexOptions({
        url: '?module=reporter&action=getConsoleDLP' + params
    }).flexReload();
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

    
    $('#multiple_groups').click(function(){
        if ($(this).is(':checked')){
            $('#group_field').attr('multiple', 'multiple')
        }else {
            $('#group_field').removeAttr('multiple')
        }
    });
    
    $('#multiple_apps').click(function(){
        if ($(this).is(':checked')){
            $('#app_field').attr('multiple', 'multiple')
        }else {
            $('#app_field').removeAttr('multiple')
        }
    });
    
    $.getJSON('?module=reporter&action=getATPEventsHistogram' + params, function(data) {
        if (data != ""){
            $("#atp_histogram").empty();
            
            var histdata = data;
            $.jqplot('atp_histogram', [histdata], {
                title: atp_events,
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
    
    $("#atp_console").flexigrid({
        url: '?module=reporter&action=getConsoleATP' + params,
        dataType: 'json',
        colModel : [
        {
            display: 'Date', 
            name : 'timetime', 
            width : 52, 
            sortable : false, 
            align: 'left'
        },

        {
            display: 'Username', 
            name : 'user', 
            width : 50, 
            sortable : false, 
            align: 'left'
        },

        {
            display: 'Process Name', 
            name : 'app', 
            width :60, 
            sortable : false, 
            align: 'left'
        },

        {
            display: 'Exploit Details', 
            name : 'details', 
            width :100, 
            sortable : false, 
            align: 'left'
        },

        {
            display: 'Start Address', 
            name : 'startaddress', 
            width :60, 
            sortable : false, 
            align: 'left'
        },

        {
            display: 'Priv Usage', 
            name : 'privateusage', 
            width :50, 
            sortable : false, 
            align: 'left'
        },

        {
            display: 'String',
            name : 'string', 
            width :25, 
            sortable : false, 
            align: 'left'
        },
        
        {
            display: 'Count Nop Max', 
            name : 'countnopmax', 
            width :70, 
            sortable : false, 
            align: 'left'
        },

        {
            display: 'Op. Largest Led', 
            name : 'operationlargestsled', 
            width :80, 
            sortable : false, 
            align: 'left'
        },
        
        {
            display: 'Star Nops Led Max', 
            name : 'startnopsledmax', 
            width :70, 
            sortable : false, 
            align: 'left',
            hide: true
        },

        ],
        usepager: true,
        useRp: false,
        rp: 15,
        width: 600,//545,
        height: 360,
        resizable: false,
        singleSelect: true
    });

    /*
     $(function() { 
        $('#chart2').dblclick( function (myObj) {  
            var content = myObj['target']['innerText']
            var mongoid_re = /^[a-fA-F0-9]+$/;
            if (content.match(mongoid_re)){
                var mongo_id = content;
                var redirect_url = "?module=reporter&action=consoleDlpEvent&event_id="+mongo_id
                window.location = redirect_url;
            }
        }); 
    });
    */
   
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
    
    
});