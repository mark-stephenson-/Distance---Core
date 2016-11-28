@if ( ! isset($ajax) ) 
<div id="image_list" style="overflow: scroll-y; width: 700px; height: 400px; display: none">
    <div class="upload_container">
        <input type="file" multiple="multiple" name="upload" class="file_upload_fallback" id="file_upload_fallback" style="display: block; width: 0px; height: 0px; ">
        <div id="dropzone">Click here to upload (or drag files here)</div>
    </div>
@endif

    <table class="image_table">
        <thead>
            <tr>
                <th width="75">Preview</th>
                <th width="150">Filename</th>
                <th width="270">Caption</th>
                <th width="65">Marked For Sync</th>
                <th width="80"></th>
            </tr>
        </thead>
        <tbody>
            @forelse(Resource::whereNotDeleted()->get() as $image)
                <tr>
                    <td>
                        @if ($image->isImage())
                            <img src="{{ $image->path() }}?w=75&amp;h=50" width="75" height="50" />
                        @else
                            <i class="icon-file" style="font-size: 30px"></i>
                        @endif
                    </td>
                    <td>
                        {{ $image->filename }}
                    </td>
                    <td>
                        {{ $image->caption }}
                    </td>
                    <td style="text-align: center; font-size: 20px">
                        @if ($image->sync)
                            <i class="icon-ok"></i>
                        @else
                            <i class="icon-remove"></i>
                        @endif
                    </td>
                    <td>
                        <a href="#" 
                            class="image_choose" 
                            data-image-id="{{ $image->id }}" 
                            data-image-filename="{{ $image->filename }}" 
                            data-image-caption="{{ $image->caption }}" 
                            data-image-url="{{ $image->path() }}"
                            @if ($image->isImage())
                                data-image-type="image" 
                            @else
                                data-image-type="file" 
                            @endif
                        >Use This Resource</a>
                    </td>
                </tr>
            @empty
                <p>You don't have any images!</p>
            @endforelse
        </tbody>
    </table>
@if ( ! isset($ajax) )  </div> @endif

@if ( ! isset($noJS))
    <script src="/libs/plupload/plupload.full.js"></script>
    <script src="/libs/plupload/plupload.html5.js"></script>
    <script src="/libs/plupload/plupload.flash.js"></script>
    <link rel="stylesheet" href="/libs/fancybox/jquery.fancybox.css" />
    <script src="/libs/fancybox/jquery.fancybox.pack.js"></script>
    <script>
        var imageDestination = null;
        $(document).ready(function() {

            $('.image_fancybox').on('click', function() {
                imageDestination = $(this).attr('data-image');
            });

            $('.image_fancybox').fancybox({
                overlayShow: true,
                hideOnContentClick: false,
                autoScale: false,
            });

            $(document).on('click', '.image_choose', function(e) {
                $.fancybox.close();

                var image_form = $('#' + imageDestination).closest('.image_container').find('.image_form');

                $('#' + imageDestination).val($(this).attr('data-image-id'));
                image_form.find('.image_filename').html( $(this).attr('data-image-filename') );
                image_form.find('.image_caption').html( $(this).attr('data-image-caption') );
                image_form.find('.image_remove').show();

                if ($(this).attr('data-image-type') == 'image') {
                    // Image
                    insertHTML('<img src="' + $(this).attr('data-image-url') + '" />');
                }

                imageDestination = null;
            });

            $('.image_preview a.img').fancybox({
                live: true,
                padding: 0
            });

            $('.image_remove').on('click', function(e) {
                e.preventDefault();

                $(this).hide();

                var image_form = $(this).closest('.image_container').find('.image_form');

                $(this).closest('.image_container').find('input').val(0);

                image_form.find('.image_filename').html("No Resource Selected");
                image_form.find('.image_caption').html('');
                image_form.find('.image_preview').html('');

            });

            var uploader;

            $( "#upload_progress" ).progressbar({
              value: 0
            }).hide();

            $('.fancybox').fancybox();

            uploader = new plupload.Uploader({
                runtimes: 'html5, flash',
                flash_swf_url : '/libs/plupload/plupload.flash.swf',
                browse_button: 'dropzone',
                drop_element:  'dropzone',
                max_file_size : '1.5mb',
                url : '{{ action('resources.process_upload.' . $node->collection->id) }}/',
                filters : [
                    {title : "Image files", extensions : "jpg,jpeg,png"},
                    {title : "Document files", extensions : "pdf"}
                ],

            });

            uploader.init();

            uploader.bind('BeforeUpload', function(uploader, file) {
                uploader.settings.url = '{{ action('resources.process_upload') }}/{{ $node->collection->id }}';
            });

            uploader.bind('Error', function(up, err) {
                alert( "Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : ")" ));
            });

            uploader.bind('UploadProgress', function(up, file) {
                // $('#upload_progress').find('.bar').html(Math.round(up.total.bytesPerSec / 1024) + 'Kb/s');
                $('#upload_progress').fadeIn();
                $( ".progress-label" ).html(Math.round(up.total.bytesPerSec / 1024) + 'Kb/s');
                $( "#upload_progress" ).progressbar("option", "value", uploader.total.percent);
                console.log(uploader.total.percent);
            });

            uploader.bind('UploadComplete', function(up, file) {
                    insertHTML('<img src="{{ URL::to('/') }}/file/' + file[0].name + '" />');
            });

            uploader.bind('FileUploaded', function(up, file, response) {
                console.log('Response: ' + response.response);
            });

            uploader.bind('FilesAdded', function(up, files) {
                uploader.start();
            });

        });

    function insertHTML(data) {
        // Get the editor instance that we want to interact with.
        var editor = CKEDITOR.instances.input_html;

        // Check the active editing mode.
        if ( editor.mode == "wysiwyg" )
        {
            editor.insertHtml( data );
        }
        else
            alert( 'You must be in WYSIWYG mode!' );
    }
    </script>
@endif

<style>
    .upload_container {
        text-align: center;
        width: 100%;
        margin-bottom: 20px;
    }

    #dropzone_collection {
        float: left;
        margin-top: 20px;
    }

    #dropzone {
        margin: 10px;
        padding: 10px 0;
        background: #DDDEE0;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        border-radius: 10px;
    }

    .progress-label {
        float: left;
        margin-left: 50%;
        margin-top: 5px;
        font-weight: bold;
        text-shadow: 1px 1px 0 #fff;
    }
</style>