
$('a.gallery').colorbox({
    photo:true, 
    width:"80%", 
    height:"80%"
});

$('.inline').colorbox({
    inline:true, 
    onOpen: function () {
        $("#event_map_view").show();
        drawEventMap();
    },
    onClosed: function () {
        $("#event_map_view").hide();
    }
});

function drawEventMap(){
    var eventLatitude = $("#event_lat").val();
    var eventLongitude = $("#event_lng").val();
    var eventAccuracy = parseInt($("#event_accuracy").val());
    
    var eventLatlng = new google.maps.LatLng(eventLatitude, eventLongitude)
    
    var myOptions = {
        zoom: 8,
        center: eventLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    var event_map = new google.maps.Map($("#event_map").get(0), myOptions);

    new google.maps.Marker({
        position: eventLatlng,
        animation: google.maps.Animation.DROP,
        map: event_map,
        title: "Endpoint"
    });

    var area_radius = 0;
    if (eventAccuracy < 100){
        area_radius = eventAccuracy;
    }

    new google.maps.Circle({
        strokeColor: "#0066FF",
        strokeOpacity: 0.8,
        strokeWeight: 0.5,
        fillColor: "#0066FF",
        fillOpacity: 0.25,
        map: event_map,
        center: eventLatlng,
        radius: area_radius
    });
    $("#event_map_view").hide();
}

$(document).ready(function(){
    drawEventMap();
});