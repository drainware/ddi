//////////////////////////////////////////
//		Documen ready functions			//
/////////////////////////////////////////




function DrawNumberOfEvents() {
    $.getJSON('/ddi/?module=api&action=getGlobalEvents', function(data) {
        if (data>=0) {
            /*$("#number_of_events .bar").css("width", data + "%");*/	
            $("#number_of_events .bar").html(data);
        }
    });
}



$(document).ready(function() {
    DrawNumberOfEvents();
    intervalcpu=setInterval("DrawNumberOfEvents()",1000);
	
    $('#number_of_events').click(function(){
        $.colorbox({
            href: '/ddi/?module=reporter&action=showDailyDLPEvents', 
            iframe:true, 
            width:"90%", 
            height:"70%"
        });
    });

    $('input, textarea').placeholder();
    $(".tabs").tabs();

    /*
    $('#pay_tweet').click(function(){
        $("#pay_tweet").colorbox({
            href:"https://www.paywithatweet.com/pay/?id=0cc36412007de8da00f23701bf8447cc",
            iframe:true, 
            width:"50%", 
            height:"50%"
        });
    });
    */
    $('a[href^=#]').on("click",function(){
        var t= $(this.hash);
        var t=t.length&&t||$('[name='+this.hash.slice(1)+']');
        if(t.length){
            var tOffset=t.offset().top;
            $('html,body').animate({
                scrollTop:tOffset-110
                },500);
            return false;
        }
    });
    $(window).scroll(function(){
        var scroll=$(window).scrollTop();
        $(".panel a").css("font-size","1em");
        if(scroll>=780&&scroll<1308){
            $('a[href="#data"]').css("font-size","1.1em");
        }
        if(scroll>=1308&&scroll<1753){
            $('a[href="#forensics"]').css("font-size","1.1em");
        }
        if(scroll>=1753&&scroll<2248){
            $('a[href="#sandbox"]').css("font-size","1.1em");
        }
        if(scroll>=2248&&scroll<2600){
            $('a[href="#ejemplos"]').css("font-size","1.1em");
        }
        $(".footer a").css("font-size","16px");
    });
    $('#pay_tweet').click(function(){
        open(
            'https://www.paywithatweet.com/pay/?id=c50dd785e353b71bad67a1125d0fcf4e',
            '',
            'toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=yes,width=810,height=490');
    });
    

});

