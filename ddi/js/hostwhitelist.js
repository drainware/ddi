$(document).ready(function() {

    $('input:radio[name=type]').change(function(){

       var val = $('input:radio[name=type]:checked').val();
       if (val=="ip") {
           $('#range').hide(0,function() { $('#ip').show()});
           $('#range input').removeClass('required');
           $('#ip input').addClass('required');
           
       } else {
           $('#ip').hide(0,function() { $('#range').show()});
           $('#range input').addClass('required');
           $('#ip input').removeClass('required');


       }
    });
    
});
