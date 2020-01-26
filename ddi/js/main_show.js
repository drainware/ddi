var intervalcpu=null;
var intervalmem=null;


var gridster;

$(function(){

    var dwidth  = $(window).width();

    if(dwidth >= 1024) {

      gridster = $(".gridster ul").gridster({
        widget_margins: [5, 5],
        widget_base_dimensions: [240, 120],
        min_cols: 3,
        min_rows: 20
      }).data('gridster');



    } else if (dwidth > 600 && dwidth < 1024 ){

      gridster = $(".gridster ul").gridster({
        widget_margins: [5, 5],
        widget_base_dimensions: [240, 120],
        min_cols: 2,
        min_rows: 20
      }).data('gridster').disable();


    }else{
      // mobile
      gridster = $(".gridster ul").gridster({
        widget_margins: [5, 5],
        widget_base_dimensions: [240, 120],
        min_cols: 1,
        min_rows: 20
      }).data('gridster').disable();

    }


});



$("#add_widget").click(function() {
gridster.add_widget('<li class="new">The HTML of the widget...<li>', 3, 1);
});

function DrawCpuUsage() {
  $.getJSON('?module=api&action=getCpuUsage', function(data) {
   if (data>=0) {
     $("#cpuusagebar .bar").css("width", data + "%"); 	
     $("#cpuusagebar .bar").html(data + "%");
    }
   });
}

function DrawMemUsage() {


  $.getJSON('?module=api&action=getMemUsage', function(data) {
   if (data) {
     //$("#memusagebar .bar").css("width", toString(data[2]) + "%");
     $("#memusagebar .bar").css("width", data[2] + "%");
     $("#memusagebar .bar").html(parseInt(data[1]) + "/" + parseInt(data[0] ) + "Mb");
    }
   });

}




function DrawSwapUsage() {


  $.getJSON('?module=api&action=getSwapUsage', function(data) {

   if (data) {
     //$("#memusagebar .bar").css("width", toString(data[2]) + "%");
     $("#swapusagebar .bar").css("width", data[2] + "%");
     $("#swapusagebar .bar").html(parseInt(data[1]/1024) + "/" + parseInt(data[0] / 1024) + "Mb");
    }
   });

}

function DrawDiskUsage() {

  $.getJSON('?module=api&action=getDiskUsage', function(data) {
   
   if (data>=0) {
     $("#diskusagebar .bar").css("width", data + "%");
     $("#diskusagebar .bar").html(data + "%");
    }
   });

}


function DrawNetworkStats() {


  $.getJSON('?module=api&action=getNetworkStats', function(data) {
   if (data) {
     $("#nwebfilter").html(data.nwebfilterconnections);
     $("#nadminusers").html(data.nadminconnections); 
    }
   });

}

function DrawFilterStats() {


  $.getJSON('?module=api&action=getFilterStats', function(data) {
   if (data) {
     $("#eventsusagebar .bar").css("width", parseInt(data.month_events[0]) + "%");
     $("#eventsusagebar .bar").html(parseInt(data.month_events[1]) + "/" + data.month_events[2]);
       
     $("#monthly_dlp_stats").html(data.events.monthly.dlp);
     $("#monthly_atp_stats").html(data.events.monthly.atp);
     $("#monthly_forensics_stats").html(data.events.monthly.forensics);
     $("#monthly_general_stats").html(data.events.monthly.general);
     
     $("#global_dlp_stats").html(data.events.global.dlp);
     $("#global_atp_stats").html(data.events.global.atp);
     $("#global_forensics_stats").html(data.events.global.forensics);
     $("#global_general_stats").html(data.events.global.general);
     
     $("#ngroups").html(data.groups);
    }
   })

}


$(document).ready(function(){
  DrawCpuUsage();
  DrawMemUsage();
  DrawSwapUsage();
  DrawNetworkStats();
  DrawDiskUsage();
  DrawFilterStats();

  intervalcpu=setInterval("DrawFilterStats()",5000);
  intervalcpu=setInterval("DrawCpuUsage()",5000);
  intervalmem=setInterval("DrawMemUsage()", 10000);
  intervalmem=setInterval("DrawSwapUsage()", 60000);
  intervalmem=setInterval("DrawNetworkStats()",30000); 
  intervalmem=setInterval("DrawDiskUsage()",60000);

});
