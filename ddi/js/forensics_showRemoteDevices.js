var w = 1300;
var h = 800;
var r = 500;
var x = d3.scale.linear().range([0, r]);
var y = d3.scale.linear().range([0, r]);
var node;
var root;

var pack = d3.layout.pack()
.size([r, r])
.value(function(d) {
    return d.size;
});

var vis = d3.select("h5").insert("svg:svg", "h2")
.attr("width", w)
.attr("height", h)
.append("svg:g")
.attr("transform", "translate(" + (w - r) / 2 + "," + 10 + ")");
//.attr("transform", "translate(" + (w - r) / 2 + "," + (h - r) / 2 + ")");


function drawEndpointNetwork() {
    
    $("#endpoint_network").find('svg').find('g').empty();

    d3.json(json_url, function(data) {
        
        $("#nro_devices").find('b').html(data.total)
        
        node = root = data;

        var nodes = pack.nodes(root);

        vis.selectAll("circle")
        .data(nodes)
        .enter().append("svg:circle")
        .attr("class", function(d) {
            return d.children ? "parent" : "child";
        })
        .attr("cx", function(d) {
            return d.x;
        })
        .attr("cy", function(d) {
            return d.y;
        })
        .attr("r", function(d) {
            return d.r;
        })
        .attr("channel", function(d) {
            return d.name;
        })
        .on("click", function(d) {
            return zoom(node == d ? root : d);
        });

        vis.selectAll("text")
        .data(nodes)
        .enter().append("svg:text")
        .attr("class", function(d) {
            return d.children ? "parent" : "child";
        })
        .attr("x", function(d) {
            return d.x;
        })
        .attr("y", function(d) {
            return d.y;
        })
        .attr("dy", ".35em")
        .attr("text-anchor", "middle")
        .style("opacity", function(d) {
            return d.r > 20 ? 1 : 0;
        })
        .text(function(d) {
            //return d.name;
            return d.name.substring(d.name.lastIndexOf('_')+ 1);
        });

        d3.select(window).on("click", function() {
            zoom(root);
        });
        
        if (last_total == data.total){
            draw_times++;
        } else{
            last_total = data.total;
        }
    });
    
    if (draw_times == 2){
        clearInterval(idinterval);
    }

    //reloadActions();
}


function zoom(d, i) {
    var k = r / d.r / 2;
    x.domain([d.x - d.r, d.x + d.r]);
    y.domain([d.y - d.r, d.y + d.r]);

    var t = vis.transition()
    .duration(d3.event.altKey ? 7500 : 750);

    t.selectAll("circle")
    .attr("cx", function(d) {
        return x(d.x);
    })
    .attr("cy", function(d) {
        return y(d.y);
    })
    .attr("r", function(d) {
        return k * d.r;
    });

    t.selectAll("text")
    .attr("x", function(d) {
        return x(d.x);
    })
    .attr("y", function(d) {
        return y(d.y);
    })
    .style("opacity", function(d) {
        return k * d.r > 20 ? 1 : 0;
    });

    node = d;
    d3.event.stopPropagation();
}


//WARNING: used enpoint_marker var instead of endpoint_marker

// 37.2 * -20 zoom:2
function drawEndpointMap(){
    var myLatitude = 37.2;
    var myLongitude = -20;
    
    var myLatlng = new google.maps.LatLng(myLatitude, myLongitude)
    
    var myOptions = {
        zoom: 2,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    
    endpoint_map = new google.maps.Map($("#endpoint_map").get(0), myOptions);
    enpoints_list = new Array();
    
    drawEndpoints();

     
}

function drawEndpoints(){
    $.ajax({
        type: 'GET',
        url: "?module=forensics&action=getRemoteDevicesMarker",
        data: {
            id: $("#remote_devices_id").val()
        },
        success: function(data) {
            var remote_devices = jQuery.parseJSON(data);
            $.each(remote_devices.markers , function(){
                Concurrent.Thread.create(drawEndpointMarker, this );
            });
        }
    });   
}

//WARNING: used enpoint_marker var instead of endpoint_marker
function drawEndpointMarker(enpoint_marker){

    if(!(enpoint_marker.id in enpoints_list)){
        enpoints_list[enpoint_marker.id] = enpoint_marker.device + ' - ' + enpoint_marker.ip;

        var device_latlng = new google.maps.LatLng(enpoint_marker.lat, enpoint_marker.lng)
        //var device_latlng = new google.maps.LatLng(Math.random() * 10 + 34.09024, Math.random() * 40 + ( -120.212391));

        var device_marker = new google.maps.Marker({
            position: device_latlng,
            animation: google.maps.Animation.DROP,
            map: endpoint_map,
            title: enpoint_marker.device + ' - ' + enpoint_marker.ip
        });


        var area_radius = 0;
        if (enpoint_marker.accuracy < 100){
            area_radius = enpoint_marker.accuracy;
        }

        new google.maps.Circle({
            strokeColor: "#0066FF",
            strokeOpacity: 0.8,
            strokeWeight: 0.5,
            fillColor: "#0066FF",
            fillOpacity: 0.25,
            map: endpoint_map,
            center: device_latlng,
            radius: area_radius
        });

        var device_message = '' + 
            '<div id="device_info"> <b>' + enpoint_marker.device + ' - ' + enpoint_marker.ip + ' </b><br/></br></div>' +
            '<div>' + 
            '<a class="button" style="line-height: 30px; margin-left: 35px;" href="/ddi/?module=forensics&action=showRemoteFileExplorer&device=' + enpoint_marker.device + '&ip=' + enpoint_marker.ip +  '" target="_blank">Explore</a>' + 
            '</div>'

        var device_info = new google.maps.InfoWindow({
            content: device_message
        });

        google.maps.event.addListener(device_marker, 'click', function() {
            device_info.open(endpoint_map,device_marker);
        });

    }
    
}


$(document).ready(function(){
    json_url = "?module=forensics&action=getRemoteDevices&id=" + $("#remote_devices_id").val();
    
    draw_times = 0;
    last_total = 0;
    drawEndpointNetwork();
    idinterval = setInterval("drawEndpointNetwork()",2500);
    
    drawEndpointMap();

    $("#map_view_button").click(function(){
        if($(this).attr("disabled", false)){
            $(this).attr("disabled", "disabled");
            $("#network_view").hide(); 
            $("#map_view").show();
            $("#network_view_button").removeAttr("disabled");
        }
    });
    
    $("#network_view_button").click(function(){
        if($(this).attr("disabled", false)){
            $(this).attr("disabled", "disabled");
            $("#map_view").hide();
            $("#network_view").show();
            $("#map_view_button").removeAttr("disabled");
        }

    });


    
});
