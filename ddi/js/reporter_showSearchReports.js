$(document).ready(function(){
    
    $( "#datepickerstart" ).datepicker({
        dateFormat: 'yy-mm-dd'
    });
    $( "#datepickerend" ).datepicker({
        dateFormat: 'yy-mm-dd'
    }); 
    
    filter()
    
    $('#search_reports_filter').click(function(){
        filter();
    });
    
    $("#remove_reports").click(function(){
        if (confirm('Are you sure, that you want to delete selected report? ', 'Remote Search Report Alert')){
            $.ajax({
                type: 'POST',
                url: '?module=reporter&action=removeSearchReport',
                data: {
                    id: $("#reports").val()
                }
            }).done(function() {
                filter();
                dwShowNotification("default", {
                    title:'Search Reports', 
                    text:'The report was successfully deleted'
                });
            });
            
        }
    });
});

function filter(){
    $("#reports").empty();
    $.ajax({
        type: 'POST',
        url: $("#get_search_reports_form").attr('action'),
        data: $("#get_search_reports_form").serialize()
    }).done(function(data) {
        var list_report = jQuery.parseJSON(data);
        $.each(list_report, function() {
            var report = '<option value="' + this._id.$id + '">' + 
            this.datetime + ' - ' + this.name +
            '</option>';
            $("#reports").append(report);
        });
    });
}