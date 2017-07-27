$(document).ready(function() {

    $('.select2').select2({

    });

    $("[rel='tooltip']").tooltip({
        container: 'body'
    });

});

var resourceUploader;
function loadResourceUploader(uploadUrl, completeCallback, uploadedCallback, filters, options) {

    if (!resourceUploader) {

        if (!filters) {
            filters = [
                {title : "Image files", extensions : "jpg,jpeg,png"},
                {title : "Document files", extensions : "pdf"}
            ];
        }

        if (!options.fileSize) {
            options.fileSize = '10';
        }

        resourceUploader = new plupload.Uploader({
            runtimes : 'html5, flash',
            flash_swf_url : '/libs/plupload/plupload.flash.swf',
            browse_button : 'dropzone',
            drop_element : 'dropzone',
            max_file_size : options.fileSize + 'mb',
            url : uploadUrl,
            filters : filters
        });

        resourceUploader.init();

        resourceUploader.bind('FilesAdded', function(up, files) {
            up.start();
        });

        resourceUploader.bind('UploadProgress', function(up, file) {
            $('#upload_progress').fadeIn();
            $("#upload_progress .bar").css('width', up.total.percent + '%');
        });

        resourceUploader.bind('UploadComplete', function(up, file) {
            if (completeCallback !== undefined) {
                completeCallback();
            }
        });

        resourceUploader.bind('FileUploaded', function(up, file, response) {
            if (uploadedCallback !== undefined) {
                uploadedCallback(response);
            }
        });

        resourceUploader.bind('Error', function(up, err) {
            alert( "Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : ")" ));
        });

    }

}

function processSingleUploadedFile(uploader, files) {
    if (files.length > 1) {
        alert('You must select only one file.');
    }

    if (files.length == 1) {
        return true;
    }

    return false;
}

var Prase = {
    alert: function (type, msg, container) {
        var alertStr = '' +
            '<div class="alert alert-' + type + ' alert-dismissible" role="alert">'
                + msg +
            '</div>';
        var $existingAlert = $(container).find('.alert');

        // if an existing alert is present, we insert the new alert after it and fade it out
        if($existingAlert.length) {
            $existingAlert.after(alertStr);
            $existingAlert.fadeOut({
                complete: function() {
                    $(this).remove();
                },
                duration: 400
            });
        } else {
            $(container).prepend(alertStr);
        }
    }
}



