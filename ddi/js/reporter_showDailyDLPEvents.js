/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function(){
    
    $.ajax({
        type: 'post',
        url: '?module=reporter&action=getDailyDLPEvents',
        data: {
        }
    }).done(function(data){
        data = jQuery.parseJSON(data);
        
        $('#container').highcharts({
            chart: {
                zoomType: 'x',
                spacingRight: 20
            },
            title: {
                text: 'Detected Threats'
            },
            subtitle: {
                text: document.ontouchstart === undefined ?
                'Click and drag in the plot area to zoom in' :
                'Pinch the chart to zoom in'
            },
            xAxis: {
                type: 'datetime',
                maxZoom: 14 * 24 * 3600000, // fourteen days
                title: {
                    text: null
                }
            },
            yAxis: {
                title: {
                    text: 'Threats'
                }
            },
            tooltip: {
                shared: true
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                area: {
                    fillColor: {
                        linearGradient: {
                            x1: 0, 
                            y1: 0, 
                            x2: 0, 
                            y2: 1
                        },
                        stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                        ]
                    },
                    lineWidth: 1,
                    marker: {
                        enabled: false
                    },
                    shadow: false,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },
    
            series: [{
                type: 'area',
                name: 'Threats',
                data: data
            }]
        });  
          
    });
    
    
});