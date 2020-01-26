var cssLink = document.createElement("link")
cssLink.href = "css/drainware_v2.css?v=1";
cssLink.rel = "stylesheet";
cssLink.type = "text/css";
//frames['info_frame'].document.body.appendChild(cssLink);

/* body onload jquery hook */
$(document).ready(function($) {

    //Notification
    $notification_container = $("#notification_container").notify();
    
    //Desktop Notification
    if(notify.isSupported){
        if ($("#notification_status").val() == "enabled"){
            if(notify.permissionLevel() == notify.PERMISSION_GRANTED){
                getLastEventNotifications();
            } else {
                if(window.location.search != '?module=main&action=showNotifications'){
                    /*
                    dwShowNotification("withIcon", {
                        title: 'Warning!', 
                        text: 'This browser not have permission to show desktop notification. Click me to configure.', 
                        icon: 'images/notification_alert.png'
                    },{ 
                        click: function(){
                            window.location.href = '?module=main&action=showNotifications';
                        },
                        expires:false
                    });
                    */
                   var message = '<div class="nwarning notification_bar">This browser have not permission to show desktop notification. Click <a href="?module=main&action=showNotifications">here</a> to configure it.</div>'
                   $('#notifications_placeholder').append(message);
                }
            }
        }
    }
    
    //Limit events warning
    /*
    var limit_msg = $('#notification_bar').html().trim();
    if(limit_msg != ""){
        dwShowNotification("withIcon", {
            title: 'Warning!', 
            text: limit_msg, 
            icon: 'images/notification_alert.png'
        },{ 
            expires:false
        });
    }
    */
   
    // FormToWizard
    $(".wizard_form").formToWizard({
        submitButton: 'saveForm'
    });


    // Equal Colums Height
    equalHeight();

    // Tipsy load
    /*
    $('#header_nav a').tipsy({
        gravity: 'n'
    });
    */

    // Accordions
    $('.accordion legend').click(function(){
        $this = $(this).parent();
        if( $this.hasClass('active') ){
            $this.removeClass('active').find('p').slideUp();
            $this.removeClass('active').find('ul').slideUp();
        }else{
            $this.addClass('active').find('p').slideDown();
            $this.addClass('active').find('ul').slideDown();
        }
    })
    
    // Accordions
    $('.results_accordion legend').click(function(){
        var $this = $(this).parent();
        if( $this.hasClass('active') ){
            $this.removeClass('active').find('p').slideUp();
            $this.removeClass('active').find('ul').slideUp();
        }else{
            $this.addClass('active').find('p').slideDown();
            $this.addClass('active').find('ul').slideDown();
            $(this).find('span').show();
        }
    })
    
    // Sidenav Options
    $('#sidenav ul.js li a').click( function( e ){
        e.preventDefault();

        var $this = $(this),
        section_opt = $this.attr('id'),
        section = section_opt.substr( ( section_opt.indexOf('opt_') + 4 ), section_opt.length );

        $('.content_section').hide().filter('#' + section ).show();
        $('#sidenav ul li a.active').removeClass('active');
        $this.addClass('active');
        equalHeight();
        positionFooter();
    } );
    
    var search_url = "https://drainware.uservoice.com/search?filter=articles&query=" ;

    // Clean search input
    $('#search_clean_pseudobutton').click( function(){
        $('#search_input').val('');
    } );

    // Submit search form
    $('#header_search_form').submit( function(e){
        //e.preventDefault();
        });

   

    $("#search_input").keypress(function(e) {
        if(e.keyCode == 13){
            search_url += $("#search_input").val();
            $.colorbox({
                href:search_url, 
                iframe:true, 
                width:"90%", 
                height:"90%"
            });
            
            search_url = "https://drainware.uservoice.com/search?filter=articles&query=";
        }
    });

    $('#guide_search').click(function(){
        search_url += $("#search_input").val();
        $('#guide_search').colorbox({
            href:search_url, 
            iframe:true, 
            width:"90%", 
            height:"90%"
        });
        
        search_url = "https://drainware.uservoice.com/search?filter=articles&query=";

    });


    //$('#guide_search').colorbox({html:'<iframe src="http://guide.drainware.com/" style="width:100%;height:100%" id="search_results"></iframe>', width:"90%", height:"90%"});



    // Footer to Bottom
    var footerHeight = 0,
    footerTop = 0,
    $footer = $("#footer_wrap"),
    bodyHeight = 0,
    windowHeight;


    positionFooter();
    function positionFooter(){

    }

    function positionFooter_3(){
        $('#main_content, #wrap, #sidenav').css('height', 'auto' );
        footerTop = ($(window).scrollTop()+$(window).height()-footerHeight);


        if(1==1){
            $footer.css({
                position: "absolute",
                bottom: 0
            });
        }else{
            $footer.css({
                position: "static"
            });
        }

        $('#main_content, #wrap, #sidenav').css('height', (footerTop - 150) +"px" );
    }
    function positionFooter_2() {
        footerHeight = $footer.height();
        footerTop = ($(window).scrollTop()+$(window).height()-footerHeight);
        bodyHeight = ($(document.body).height()+footerHeight);
        windowHeight = ( $('#main_content').height() );

        console.log(footerHeight, footerTop, bodyHeight,  windowHeight );

        if ( bodyHeight < windowHeight ) {
            $footer.css({
                position: "absolute",
                top: footerTop + "px"
            });
            $('#main_content, #wrap').css('height', (footerTop - 10) +"px" );

        } else {
            $footer.css({
                position: "static"
            });
        }

    }

    function equalHeight(){
        var highestCol = 0;

        $('.equal_column').css({
            'min-height': ''
        });

        highestCol = Math.max( $('#sidenav').height(), $('#main_content').height() );

        $('.equal_column').css({
            'min-height': highestCol
        });
    }

    // Autotab and filter for forms
    $('.ipgroup').autotab_magic().autotab_filter('numeric');

    // Plus button on forms (where avaible)
    $('.plus_option').click( function(){
        var $this = $(this);
        var layerAction = $this.attr('name');
        var $layer = $('#' + layerAction);

        if( ($layer).is(':visible') ){
            $layer.fadeOut();
            $this.attr('src', 'images/plus.png');
        }else{
            $('.slidedown_section').hide();
            $('.plus_option').attr('src', 'images/plus.png');
            $layer.fadeIn();
            $this.attr('src', 'images/less.png');
        }

    });

    // Validating forms
    //jQuery.validator.messages.required = "O_o";
    $("#wrap_content form:first").validate({
        onfocusout: true,
        errorPlacement: function(error, element) {}
    });

    // Close dialog system
    $('.close-dialog').click(function(){
        $(this).parent().fadeOut();
    });
    

//if ($('input[name="ddiwebfilter"]:checked').val()=="off") {
//$('input[name="ddiav"]').filter('[value=off]').attr('checked', true);
//$('input[name="ddiav"]').attr('disabled', true);
//}


//$('input[name="ddiwebfilter"]').change(function() {
//if ($(this).val()=="off") {
//$('input[name="ddiav"]').attr('disabled',true);
//} else {
//$('input[name="ddiav"]').attr('disabled',false);
//}
//});

});

/* end body block */

function openCloseDiv(div){
    var e = document.getElementById(div);
    var id = '#' + div;

    if(e.style.display == 'none'){
        $(id).slideDown();
    }else{
        $(id).slideUp();
    }
}

function changeOption(form){
    form.ddiav[1].checked = true;

}


function selDeSel(state, check){
    var mark = state == 'sel' ? 'checked' : '';

    var checkboxes = document.categories.catcheck;
    //var checkboxes = document.categories.cat;

    for(i = 0; i < checkboxes.length; i++){
        checkboxes[i].checked = mark;
    }
}

function selDeSel2(state, check){
    var mark = state == 'sel' ? 'checked' : '';

    var checkboxes = document.extensions.catcheck;
    //var checkboxes = document.extensions.cat;

    for(i = 0; i < checkboxes.length; i++){
        checkboxes[i].checked = mark;
    }
}

function changeSelDeSel(){
    var mark = '';

    if (document.categories.all_categories.checked == 1)
    {
        mark = 'checked';
    }

    var checkboxes = document.categories.catcheck;

    for(i = 0; i < checkboxes.length; i++){
        checkboxes[i].checked = mark;
    }
}

function changeSelDeSel2(){
    var mark = '';

    if (document.extensions.all_extensions.checked == 1)
    {
        mark = 'checked';
    }

    var checkboxes = document.extensions.extcheck;

    for(i = 0; i < checkboxes.length; i++){
        checkboxes[i].checked = mark;
    }
}

function changeSelDeSel3(){
    var mark = '';

    if (document.protocols.all_protocols.checked == 1)
    {
        mark = 'checked';
    }

    var checkboxes = document.protocols.protocheck;

    for(i = 0; i < checkboxes.length; i++){
        checkboxes[i].checked = mark;
    }
}

function isIP(number) {
    var sub = parseInt(number);
    if ((isNaN(sub)) || (sub <0) || (sub >255)) {
        return false;
    } else {
        return true;
    }
}

function isMask(number) {
    var sub = parseInt(number);
    if ((isNaN(sub)) || (sub <0) || (sub >32)) {
        return false;
    } else {
        return true;
    }
}


function sleep(milliseconds) {
  var start = new Date().getTime();
  for (var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds){
      break;
    }
  }
}

function getLastEventNotifications(){
    var id = ''
    setInterval(function(){
        $.ajax({
            type: "POST",
            url: "?module=api&action=getEventsNotification",
            data: {
                id: id
            }
        }).done(function(data) {
            data = jQuery.parseJSON(data);
            
            if(data.last_id != null){
                id = data.last_id;
            }
            
            $.each(data.notifications , function(){
                dwShowDesktopNotfication(this.title, this.body);
                sleep(2500);
            });
            
        }).always(function() {
            });
    },5000);
}

function dwShowNotification( template, vars, opts ){
    return $notification_container.notify("create", template, vars, opts);
}

function dwShowDesktopNotfication(title, body){
    notify.createNotification(title, {
        body: body,
        icon : 'images/drainware_circle.png'
    });
}
