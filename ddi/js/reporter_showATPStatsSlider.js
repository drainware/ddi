//Global variables

var table = null;
var piedata = null;

//end global variables

$(document).ready(function(){
    
    var field_by_list = ['app', 'group']
    field_by_list.forEach(function(field){
        showPieBy(field);
        showTableBy(field);
        
        $('#tb-atp-' + field).change(function(){
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

function showPieBy(field) {
    var query = '&period=' + $('#tb-atp-' + field).val();
    var fname = field.charAt(0).toUpperCase() + field.slice(1);
    $.getJSON('?module=reporter&action=getATPPieBy' + fname + query, function(data) {
        $('#atp_msg_' + field).show();
        $('#atp_pie_' + field).hide();
        $('#atp_table_' + field).parent().parent().hide();
        $('#atp_pie_' + field).empty();
        if(data.length > 0){
            $('#atp_msg_' + field).hide();
            $('#atp_pie_' + field).show();
            $('#atp_table_' + field).parent().parent().show();
            piedata = data;
            jQuery.jqplot ('atp_pie_' + field, [piedata],
            { 
                title: eval('t_atp_stats_by_' + field),
                seriesDefaults: { 
                    // Make this a pie chart.
                    renderer: jQuery.jqplot.PieRenderer,
                    rendererOptions: {
                        fill: false,
                        showDataLabels: true, 
                        sliceMargin: 4, 
                        lineWidth: 5
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
    var fname = field.charAt(0).toUpperCase() + field.slice(1);
    var query = '&period=' + $('#tb-atp-' + field).val();
    
    $('#atp_table_' + field).flexigrid({
        url: '?module=reporter&action=getATPTableBy' + fname + query,
        dataType: 'json',
        colModel : [
        {
            display: eval('t_atp_' + field), 
            name : field, 
            width : 505, 
            sortable : false, 
            align: 'left'
        },

        {
            display: t_atp_number_of_records , 
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
    var fname = field.charAt(0).toUpperCase() + field.slice(1);
    var query = '&period=' + $('#tb-atp-' + field).val();
    $('#atp_table_' + field).flexOptions({
        url: '?module=reporter&action=getATPTableBy' + fname + query
    }).flexReload();
}


