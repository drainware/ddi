/* Created by jankoatwarpspeed.com */

(function($) {
    $.fn.formToWizard = function(options) {
        options = $.extend({
            submitButton: ""
        }, options);

        var element = this;
        
        var steps = $(".dwstep").closest('fieldset')
        var count = steps.size();
        var submmitButtonName = "#" + options.submitButton;
        $(submmitButtonName).hide();

        // 2
        $(element).before("<ul id='steps'></ul>");

        steps.each(function(i) {
            $(this).wrap("<div id='step" + i + "'></div>");
            $(this).append("<p id='step" + i + "commands'></p>");

            // 2
            var name = $(this).find("legend").html();
            $("#steps").append("<li id='stepDesc" + i + "'>" + wizard_step +" " + (i + 1) + "<span>" + name + "</span></li>");

            if (i == 0) {
                createCancelButton();
                createNextButton(i);
                selectStep(i);
            }
            else if (i == count - 1) {
                $("#step" + i).hide();
                createPrevButton(i);
            }
            else {
                $("#step" + i).hide();
                createPrevButton(i);
                createNextButton(i);
            }
        });

        function createCancelButton(){
          $('#step0commands').append('<a href="/" class="prev button red">' + wizard_cancel + '</a>');
        }

        function createPrevButton(i) {
            var stepName = "step" + i;
            $("#" + stepName + "commands").append("<a href='#' id='" + stepName + "Prev' class='prev button red'>&lt; " + wizard_previous + "</a>");

            $("#" + stepName + "Prev").bind("click", function(e) {

                if (i==2) {

                   if ($("#dhcp").is(':checked')) { 
                    i=1;
                   }
                    $("#step1 input[type=text]").addClass("required");
                    

                }
                $("#" + stepName).hide();
                $("#step" + (i - 1)).show();
                $(submmitButtonName).hide();
                selectStep(i - 1);
            });
        }

        function createNextButton(i) {
            var stepName = "step" + i;
            $("#" + stepName + "commands").append("<a href='#' id='" + stepName + "Next' class='next button'>" + wizard_next + " &gt;</a>");

            $("#" + stepName + "Next").bind("click", function(e) {
                //added code by antonio.moreno@drainware.com
               var jump = i;  
               if (i==0) {
                
                   if ($("#dhcp").is(':checked')) {
                    jump=1;
                    $("#step1 input[type=text]").removeClass("required"); 
                   } else {
                    jump=0;
                   } 

                }
                var zoneForm = $(this).closest('div');
                var flagWizards = zoneForm.find('input').valid();
                if(flagWizards == 1){
                  $("#" + stepName).hide();
                  $("#step" + (jump + 1)).show();
                  if (i + 2 == count)
                      $(submmitButtonName).show().appendTo("#step" + (i + 1) + "commands");
                  selectStep(jump + 1);
                }
            });
        }

        function selectStep(i) {
            $("#steps li").removeClass("current");
            $("#stepDesc" + i).addClass("current");
        }

    }
})(jQuery);
