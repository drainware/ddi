
var filter_by_group = false;
var filter_by_user = false;
var filter_by_ip = false;
var params_string = '';

function filter() {
        
    $("#chart1, #chart2, #chart3").empty();
    $("#chart2-wrapper").hide();
    $("#chart1").show();      
    
    params_string = '';
    
    var start = $("#datepickerstart").val();
    var end = $("#datepickerend").val();
    
    if ((start!='') && (end!='')) {
        params_string = params_string + '&start=' + start + '&end=' + end;
    }
    
    if (filter_by_group) {
        params_string = params_string + '&group=' + $("#group").val();
    } else if (filter_by_ip) {
        params_string = params_string + '&ip=' + $("#r1_1").val() + '.' + $("#r1_2").val() + '.' + $("#r1_3").val() + '.' + $("#r1_4").val();
    } else if (filter_by_user) {
        params_string = params_string + '&user=' + $("#user_field").val();         
    }

    var redirect_url = "?module=reporter&action=urlDetails&url="+params['url']+params_string;
    window.location = redirect_url;

}

function getUrlParams() {
    
    params_string = '';
    params = {};
    window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        if (key == "action"){
            value = "getAccessHistogramByUrl";
        }
        params[key] = value;
        params_string = params_string + key + "=" + value + "&";
    });
    return params_string;
}

function hideHistogramByUrl() {
    var redirect_url = "?module=reporter&action=show"
    window.location = redirect_url;
}

$(document).ready(function(){

    $( "#datepickerstart" ).datepicker({
        dateFormat: 'yy.mm.dd'
    });
    $( "#datepickerend" ).datepicker({
        dateFormat: 'yy.mm.dd'
    }); 

    $.getJSON('?'+getUrlParams(), function(data) {

        var histdata = data;
        $("#chart2-wrapper").show();
        $("#chart1").hide()
        $("#chart2").empty();
        var plot1 = $.jqplot('chart2', [histdata], {
            title: params['url'],
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
    });

    function configureOptions(){
    
        var value = parseInt($('#filter').val());
  
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
    }

    configureOptions();

    $('#filter').change(function() {
        configureOptions();
    });

});