/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$(function() {
        $("#uploader").pluploadQueue({
                // General settings
                runtimes : 'html5,gears,flash,silverlight,browserplus',
                url : '?module=dlp&action=createFile',
                max_file_size : '800mb',
                chunk_size : '2mb',
                unique_names : true,
                multipart : true,
                multipart_params: { 'dwFileName': '' },

                // Resize images on clientside if we can
                //resize : {width : 320, height : 240, quality : 90},

                // Specify what files to browse for
                filters : [
                        {title : "Source code", extensions : "js,cs,cpp,c,h,hpp,php,py,pl"},
                        {title : "CD and DVD images", extensions : "iso,dmg"},
                        {title : "Image files", extensions : "jpg,gif,png"},
                        {title : "Office files", extensions : "doc,docx,pdf,ppt,pptx,xls,xlsx,rtf,txt,mdb"},
                        {title : "Zip files", extensions : "zip,7z,rar,gz,bz2,tgz"}
                ],

                // Flash settings
                flash_swf_url : 'js/plupload/plupload.flash.swf',

                // Silverlight settings
                silverlight_xap_url : 'js/plupload/plupload.silverlight.xap'
        });

        // Client side form validation
     
     $('#uploader').pluploadQueue().bind('BeforeUpload', function(up, file, res) {
            up.settings.multipart_params['dwFileName'] = file.name;
     });    
     
     $('#uploader').pluploadQueue().bind('FileUploaded', function(up, file, res) {              
          if( (up.total.uploaded ) == up.files.length)
          {
            alert("Uploading completed");
            location.href = "?module=dlp&action=showFiles";
          }
     });
         $('form').submit(function(e) { 
        var uploader = $('#uploader').pluploadQueue();
            
        // Files in queue upload them first
        if (uploader.files.length > 0) {
            // When all files are uploaded submit form
            uploader.bind('StateChanged', function() {
                if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                    $('form')[0].submit();
                }
            });
                
            uploader.start();

        } else {
            alert('You must queue at least one file.');
        }

        return false;
    });
});