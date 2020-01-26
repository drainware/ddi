//Global variables

var table = null;
var piedata = null;

//end global variables

$(document).ready(function(){

    $('.time-bar select').each(function(){
        //$(this).selectToUISlider().next();
    });

    $('#handle_tb-activity').change(function(){
       //alert('O.o'); 
    });

    showActivityBubble();
    showActivityTable();

    $('#tb-activity').change(function(){
        showActivityBubble();
        updateActivityTable();
    });

    var field_policy_by_list = ['group', 'user']
    field_policy_by_list.forEach(function(field){
        showPolicyBarBy(field);
        showPolicyTableBy(field);
        
        $('#tb-' + field + '_policy').change(function(){
            showPolicyBarBy(field);
            updatePolicyTableBy(field);
        });
        
        $('#ps-' + field + '_policy').change(function(){
            showPolicyBarBy(field);
            updatePolicyTableBy(field);
        });
    }); 

    var field_by_list = ['policy', 'group']
    field_by_list.forEach(function(field){
        showPieBy(field);
        showTableBy(field);
        
        $('#tb-' + field).change(function(){
            showPieBy(field);
            updateTableBy(field);
        });
    });
    
    $('#slider-id').codaSlider({
        continuous:true,
        dynamicTabsAlign: "left",
        dynamicArrows: false
    });
});

function showActivityBubble() {
    var query = '&period=' + $('#tb-activity').val();
    $.getJSON('?module=reporter&action=getDlpBubbleActivity' + query, function(data) {
        $("#dlp_msg_activity").show();
        $("#dlp_bubble_activity").hide();
        $("#dlp_table_activity").parent().parent().hide();
        $("#dlp_bubble_activity").empty();
        if(data.length > 0){
            $("#dlp_msg_activity").hide();
            $("#dlp_bubble_activity").show();
            $("#dlp_table_activity").parent().parent().show();
            bubbledata = data;
            jQuery.jqplot ('dlp_bubble_activity', [bubbledata],
            { 
                title: t_dlp_stats_activity, 
                seriesDefaults:{
                    renderer: $.jqplot.BubbleRenderer,
                    rendererOptions: {
                        bubbleAlpha: 0.6,
                        highlightAlpha: 0.8
                    },
                    shadow: true,
                    shadowAlpha: 0.05
                }
            });
        }
    });
}

function showActivityTable(){
    var query = '&period=' + $('#tb-activity').val();
    $("#dlp_table_activity").flexigrid({
        url: '?module=reporter&action=getDlpTableActivity' + query,
        dataType: 'json',
        colModel : [
        {
            display: t_dlp_policy, 
            name : 'policy', 
            width : 300, 
            sortable : false, 
            align: 'left'
        },
        {
            display: t_dlp_action, 
            name : 'action', 
            width : 100, 
            sortable : false, 
            align: 'left'
        },
        {
            display: t_dlp_severity, 
            name : 'severity', 
            width : 100, 
            sortable : false, 
            align: 'left'
        },
        {
            display: t_dlp_number_of_records , 
            name : 'nro_records', 
            width : 153, 
            sortable : false, 
            align: 'left'
        }
        ],
        sortname: "nro_records",
        sortorder: "desc",
        resizable: false,
        singleSelect: true
    });
}

function updateActivityTable(){
    var query = '&period=' + $('#tb-activity').val();
    $('#dlp_table_activity').flexOptions({
        url: '?module=reporter&action=getDlpTableActivity' + query
    }).flexReload();
}

function showPolicyBarBy(field){
    var fname = field.charAt(0).toUpperCase() + field.slice(1);
    field = field + '_policy';
    var query = '&period=' + $('#tb-' + field).val() + '&policy=' + $('#ps-' + field).val();
    
    $.getJSON('?module=reporter&action=getDlpPolicy' + fname + 'Bar' + query, function(data) {
        $('#dlp_msg_' + field).show();
        $('#dlp_bar_' + field).hide();
        $('#dlp_table_' + field).parent().parent().hide();
        $('#dlp_bar_' + field).empty();
        if(data.length > 0){
            $('#dlp_msg_' + field).hide();
            $('#dlp_bar_' + field).show();
            $('#dlp_table_' + field).parent().parent().show();
            piedata = data;
            var plot1 = jQuery.jqplot ('dlp_bar_' + field, [piedata],{ 
                title: eval('t_dlp_stats_' + field),
                series:[{
                    renderer:$.jqplot.BarRenderer
                    }],
                axesDefaults: {
                    tickRenderer: $.jqplot.CanvasAxisTickRenderer ,
                    tickOptions: {
                        fontFamily: 'Georgia',
                        fontSize: '10pt',
                        angle: -30
                    }
                },
                axes: {
                    xaxis: {
                        renderer: $.jqplot.CategoryAxisRenderer
                    }
                }
            });
        }
    });
}

function showPolicyTableBy(field){
    var fname = field.charAt(0).toUpperCase() + field.slice(1);
    field = field + '_policy';
    var query = '&period=' + $('#tb-' + field).val() + '&policy=' + $('#ps-' + field).val();
    
    $('#dlp_table_' + field).flexigrid({
        url: '?module=reporter&action=getDlpPolicy' + fname + 'Table' + query,
        dataType: 'json',
        colModel : [
        {
            display: eval('t_dlp_' + field), 
            name : field, 
            width : 505, 
            sortable : false, 
            align: 'left'
        },

        {
            display: t_dlp_number_of_records , 
            name : 'nro_records', 
            width : 170, 
            sortable : false, 
            align: 'left'
        }
        ],
        sortname: "nro_records",
        sortorder: "desc",
        resizable: false,
        singleSelect: true
    });
}

function updatePolicyTableBy(field){
    var fname = field.charAt(0).toUpperCase() + field.slice(1);
    field = field + '_policy';
    var query = '&period=' + $('#tb-' + field).val() + '&policy=' + $('#ps-' + field).val();
    $('#dlp_table_' + field).flexOptions({
        url: '?module=reporter&action=getDlpPolicy' + fname + 'Table' + query
    }).flexReload();
}

function showPieBy(field){
    var query = '&filter=' + field + '&period=' + $('#tb-' + field).val();
    $.getJSON('?module=reporter&action=getDlpPie' + query, function(data) {
        $('#dlp_msg_' + field).show();
        $('#dlp_pie_' + field).hide();
        $('#dlp_table_' + field).parent().parent().hide();
        $('#dlp_pie_' + field).empty();
        if(data.length > 0){
            $('#dlp_msg_' + field).hide();
            $('#dlp_pie_' + field).show();
            $('#dlp_table_' + field).parent().parent().show();
            piedata = data;
            var plot1 = jQuery.jqplot ('dlp_pie_' + field, [piedata],
            { 
                title: eval('t_dlp_stats_by_' + field), 
                seriesDefaults: { 
                    // Make this a pie chart.
                    renderer: jQuery.jqplot.PieRenderer,
                    rendererOptions: {
                        // Put data labels on the pie slices.
                        // By default, labels show the percentage of the slice.
                        fill: false,
                        sliceMargin: 5,
                        lineWidth: 1,
                        showDataLabels: true
                    }
                },
                legend: {
                    show:true, 
                    location: 'e'
                }
            });
        }
    });
}

function showTableBy(field){
    var query = '&filter=' + field + '&period=' + $('#tb-' + field).val();
    $('#dlp_table_' + field).flexigrid({
        url: '?module=reporter&action=getDlpTable' + query,
        dataType: 'json',
        colModel : [
        {
            display: eval('t_dlp_' + field), 
            name : field, 
            width : 505, 
            sortable : false, 
            align: 'left'
        },

        {
            display: t_dlp_number_of_records , 
            name : 'nro_records', 
            width : 170, 
            sortable : false, 
            align: 'left'
        }
        ],
        sortname: "nro_records",
        sortorder: "desc",
        resizable: false,
        singleSelect: true
    });
}

function updateTableBy(field){
    var query = '&filter=' + field + '&period=' + $('#tb-' + field).val();
    $('#dlp_table_' + field).flexOptions({
        url: '?module=reporter&action=getDlpTable' + query
    }).flexReload();
}

