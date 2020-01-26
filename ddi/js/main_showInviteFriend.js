(function( $ ){
 
    $.fn.multipleInput = function() {
 
        return this.each(function() {
 
            // create html elements
 
            // list of email addresses as unordered list
            $list = $('<ul />');
 
            // input
            var $input = $('<input type="text" id="email-element" />').keypress(function(event) {
                
                if(event.which == 13 || event.which == 32 || event.which == 44) {     
                    // key press is space or comma
                    var val = $(this).val();
                    
                    $(this).mailcheck({
                        suggested: function(element, suggestion) {  
                            //val = suggestion.full;
                            event.preventDefault();
                        },
                        empty: function(element) {
                            //val = '';
                            event.preventDefault();
                        }
                    });
                    
                    createEmailBox(val)
                    
                    // empty input
                    event.preventDefault();
                    $(this).attr('placeholder', '');
                    $(this).val('');
                }
 
            });
 
            // container /div//
            var $container = $('<div class="multipleInput-container" />').click(function() {
                $input.focus();
            });
 
            // insert elements into DOM
            $container.append($list).append($input).insertAfter($(this));
 
            // add onsubmit handler to parent form to copy emails into original input as csv before submitting
            var $orig = $(this);
            $(this).closest('form').submit(function(e) {
 
                var emails = new Array();
                $('.multipleInput-email span').each(function() {
                    emails.push($(this).html());
                });
                emails.push($input.val());
 
                $orig.val(emails.join());
 
            });
 
            return $(this).hide();
 
        });
 
    };
})( jQuery );

function createEmailBox(email){
    if(email != ""){
        
        var email_element = '<li class="multipleInput-email"><span>' + email + '</span></li>';
        var email_element_close = '<a href="#" class="multipleInput-close" title="Remove" />'
        var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

        if(emailPattern.test(email) == false){
            email_element = '<li class="multipleInput-email-error"><span>' + email + '</span></li>';
            email_element_close = '<a href="#" class="multipleInput-close-error" title="Remove" />'
        }    

        // append to list of emails with remove button
        $list.append($(email_element)
            .append($(email_element_close).click(function(e) {
                    $(this).parent().remove();
                    e.preventDefault();
            }))
        );
    }
}

function printObject(o) {
    var out = '';
    for (var p in o) {
        out += p + ': ' + o[p] + '\n';
    }
    alert(out);
}

$(document).ready(function(){
    $('#email_list').multipleInput();
    
    var oneLetterWidth = 10;
    var minCharacters = 5;
    $('#email-element').keyup(function () {
        var len = $(this).val().length;
        if (len > minCharacters) {
            // increase width
            $(this).width(len * oneLetterWidth);
        } else {
            // restore minimal width;
            $(this).width(50);
        }
    });
    
    $('#email-element').on('paste', function(){
        var element = this;
        setTimeout(function(){
            var content = $(element).val();
            var list_emails = content.split(/[\s,\n\r\t]+/);
            list_emails.forEach(function(email){
               createEmailBox(email);
            });
             $(element).val('');
        }, 100);
        
    });
    
    $('#send-invites').click(function(){
        var email_list = new Array();
        $('.multipleInput-container ul').find('.multipleInput-email span').each(function(){
            email_list.push(this.textContent);
        });
        
        email_list = email_list.filter(function(elem, pos, self){
            return (self.indexOf(elem) == pos);
        });
        
        if (email_list.length != 0){
            var button_value = $('#send-invites').val();
            $('#send-invites').val('Sending...');
            $('#send-invites').attr('disabled', 'disabled');
            $('#sending_email').show();
            
            $.ajax({
                type: "POST",
                url: "?module=main&action=sendInvitations",
                data: {
                    emails : email_list
                }
            }).done(function(data) {
                data = jQuery.parseJSON(data);
                
                var message = '1 person was succesfully invite to Drainware';
                if(data.guests.length > 0){
                    if (data.guests.length > 1){
                        message = data.guests.length + ' people were succesfully invite to Drainware';
                    }
                    dwShowNotification("default", {
                        title:'Invite by email', 
                        text: message
                    });
                }
                if(data.clients.length > 0){
                    message = 'These people already use Drainware: ' + data.clients.join(); 
                    dwShowNotification("default", {
                        title:'Invite by email', 
                        text: message
                    });
                }
                
                $('.multipleInput-container ul').empty();
            }).always(function() {
                $('#send-invites').val(button_value);
                $('#send-invites').removeAttr('disabled');
                $('#sending_email').hide();
            }); 
        }
        
        
    });
    
    $('#invite-twitter').click(function(){
        window.open(
            'https://twitter.com/intent/tweet?url=' + referral_url+ '&text=' + invite_message, 
            'twitter-share-dialog', 
            'width=626,height=436'
            );
    });
    
    $('#invite-facebook').click(function(){
        window.open(
            'https://www.facebook.com/sharer/sharer.php?u=' + referral_url, 
            'facebook-share-dialog', 
            'width=626,height=436'
            );
    });
    
    
    function animateCopy(){
        $('#copy-text').show()
        .css({
            top:-45,
            position:'relative', 
            opacity:1
        })
        .animate({
            opacity:0,
            top:-100
        }, 1000)
        .animate({
            top:100,
            position:'relative'
        }, 0);  
    }
    
    $("#copy-link").zclip({
        path: "js/zclip/ZeroClipboard.swf",
        copy: function(){
            animateCopy();
            return $(this).prev().val();
        },
        afterCopy: function(){
        }
    });
    
});
    
