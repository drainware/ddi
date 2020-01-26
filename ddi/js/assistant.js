$(document).ready(function($) {

$("#nextwelcome").click(function() {

$("#welcome").fadeOut('slow',function() { $("#stepa").fadeIn('slow'); });

});

$("#nextstepa").click(function() {
var varcontrol = $("#stepa #static:checked");
if (varcontrol.length>0) {
$("#stepa").fadeOut('slow',function() { $("#stepb").fadeIn('slow'); });
} else {
$("#stepa").fadeOut('slow',function() { $("#end").fadeIn('slow'); });
}

});

$("#nextstepawizard").click(function() {
var varcontrol = $("#stepa #static:checked");
if (varcontrol.length>0) {
$("#stepa").fadeOut('slow',function() { $("#stepb").fadeIn('slow'); });
} else {
$("#stepa").fadeOut('slow',function() { $("#form-assistant").submit(); });
}

});

$("#nextstepb").click(function() {

var ipstatic = false;
var mask = false;
var gateway = false;



if (isIP($('[name=group1a]').val())&&isIP($('[name=group2a]').val())&&isIP($('[name=group3a]').val())&&isIP($('[name=group4a]').val())) {
 
  ipstatic = true;

} else {
  
  alert("La IP estatica incorrecta");
  return false;
 
}

if (isIP($('[name=group1b]').val())&&isIP($('[name=group2b]').val())&&isIP($('[name=group3b]').val())&&isIP($('[name=group4b]').val())) {
 
  mask = true;

} else {
  
  alert("La mascara es incorrecta");
  return false;
 
}

if (isIP($('[name=group1c]').val())&&isIP($('[name=group2c]').val())&&isIP($('[name=group3c]').val())&&isIP($('[name=group4c]').val())) {
 
  gateway = true;

} else {
  
  alert("La IP de la puerta de enlace es incorrecta");
  return false;
}


$("#stepb").fadeOut('slow',function() { $("#stepc").fadeIn('slow'); });


});



$("#nextstepc").click(function() {

var dns1 = false;
var dns2 = false;



if (isIP($('[name=dnsgroup1a]').val())&&isIP($('[name=dnsgroup2a]').val())&&isIP($('[name=dnsgroup3a]').val())&&isIP($('[name=dnsgroup4a]').val())) {
 
  dns1 = true;

} else {
  
  alert("La IP del primer DNS es incorrecta");
  return false;
 
}

if (isIP($('[name=dnsgroup1b]').val())&&isIP($('[name=dnsgroup2b]').val())&&isIP($('[name=dnsgroup3b]').val())&&isIP($('[name=dnsgroup4b]').val())) {
 
  dns2 = true;

} else {
  
  alert("La IP del segundo DNS es incorrecta");
  return false;
 
}

$("#stepc").fadeOut('slow',function() { $("#end").fadeIn('slow'); });

});




$("#nextend").click(function() {

$("#form-assistant").submit();

});

$("#redoend").click(function() {

location.reload(true);

});

$("#addroute").click(function() {

location.href="/ddi/?module=wizard&action=step2";

});

$("#reboot").click(function() {

location.href="/ddi/?module=wizard&action=reboot";

});

$("#createroute").click(function() {

var ipstatic = false;
var mask = false;
var gateway = false;


if (isIP($('#newroute [name=group1a]').val())&&isIP($('#newroute [name=group2a]').val())&&isIP($('#newroute [name=group3a]').val())&&isIP($('#newroute [name=group4a]').val())) {
  ipstatic = true;
} else {
  alert("La IP estatica incorrecta");
  return true;
}



if (isMask($('#newroute [name=mask]').val())) {
  mask = true;
} else { 
  alert("La mascara es incorrecta");
  return true;
}

if (isIP($('#newroute [name=group1b]').val())&&isIP($('#newroute [name=group2b]').val())&&isIP($('#newroute [name=group3b]').val())&&isIP($('#newroute [name=group4b]').val())) {
  gateway = true;
} else {
  alert("La IP de la puerta de enlace es incorrecta");
  return true;
}

$('#newroute').submit();

});


});
