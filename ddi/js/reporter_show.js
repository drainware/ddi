//Global variables

var show_all = true;
var filter_by_group = false;
var filter_by_user = false;
var filter_by_ip = false;
var table = null;

var piedata = null;
var pieblockeddata = null;
var params = '';

//end global variables

function filter() {
        
    $("#chart1, #chart2, #chart3").empty();
    $("#chart2-wrapper").hide();
    $("#chart1").show();
    var url = '';
    params = '';
    var start = $("#datepickerstart").val();
    var end = $("#datepickerend").val();
    if ((start!='') && (end!='')) {
        params = params + '&start=' + start + '&end=' + end;
    }
      
    if (filter_by_group) {
        params = params + '&group=' + $("#group").val();
    } else if (filter_by_ip) {
        params = params + '&ip=' + $("#r1_1").val() + '.' + $("#r1_2").val() + '.' + $("#r1_3").val() + '.' + $("#r1_4").val();
    } else if (filter_by_user) {
        params = params + '&user=' + $("#user_field").val();         
    }
      
    showPie(params);
    jQuery('#flex1').flexOptions({
        url: '?module=reporter&action=getAccessTable' + params
    }).flexReload();

}

$(document).ready(function(){

    $( "#datepickerstart" ).datepicker({
        dateFormat: 'yy.mm.dd'
    });
    $( "#datepickerend" ).datepicker({
        dateFormat: 'yy.mm.dd'
    }); 

    showPie('');
 
    table = $(function() {
        $("#flex1").flexigrid({
            url: '?module=reporter&action=getAccessTable',
            dataType: 'json',
            colModel : [
            {
                display: 'Url', 
                name : 'url', 
                width : 300, 
                sortable : false, 
                align: 'left'
            },

            {
                display: number_of_access , 
                name : 'count', 
                width : 200, 
                sortable : false, 
                align: 'left'
            }
            ],
            searchitems : [
            {
                display: 'Url;', 
                name : 'url', 
                isdefault: true
            }
            ],
            sortname: "count",
            sortorder: "desc",
            usepager: false,
            useRp: false,
            rp: 1,
            showTableToggleBtn: false,
            resizable: false,
            width: 540,
            height: 370,
            singleSelect: true
        });
    });
    
    $('#filter').change(function() {
        var value = parseInt($(this).val());
        switch(value)
        {
            case 0:
                $(".filter_by").hide();
                show_all = true;
                filter_by_group = false;
                filter_by_user = false;
                filter_by_ip = false;
                break;
            case 1:
                $(".filter_by").hide();
                show_all = false;
                filter_by_group = true;
                filter_by_user = false;
                filter_by_ip = false;
                $("#filter_by_group").show();
                //$(".filter_by").hide( function() { $("#filter_by_group").show()} );
                break;
            case 2:
                $(".filter_by").hide();
                show_all = false;
                filter_by_group = false;
                filter_by_user = true;
                filter_by_ip = false;
                //$(".filter_by").hide( function() { $("#filter_by_user").show()} );
                $("#filter_by_user").show()
                break;
            case 3:
                $(".filter_by").hide();
                show_all = false;
                filter_by_group = false;
                filter_by_user = false;
                filter_by_ip = true;
                $("#filter_by_ip").show()
                //$(".filter_by").hide( function() { $("#filter_by_ip").show()} );
                break;
            default:
                alert("error");
        }
    });
   
    $(function() { 
        $('#flex1').dblclick( function (myObj) {
            var content = myObj['target']['innerText']
            var number_re = /^\s*\d+\s*$/;
            if (content.match(number_re)==null){
                var url = content;
                var redirect_url = "?module=reporter&action=urlDetails&url="+url+params
                window.location = redirect_url;
            }
        }); 
    });
    
});

function showPie(params) {
    
    $.getJSON('?module=reporter&action=getAccessPie' + params, function(data) {
 	
        if(data != ""){
            $("#chart1").empty();
        
            piedata = data;

            var plot1 = jQuery.jqplot ('chart1', [piedata],
            { 
                title: most_accessed_urls, 
                seriesDefaults: { 
                    // Make this a pie chart.
                    renderer: jQuery.jqplot.PieRenderer,
                    rendererOptions: {
                        // Put data labels on the pie slices.
                        // By default, labels show the percentage of the slice.
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

