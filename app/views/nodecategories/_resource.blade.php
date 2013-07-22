<div id="resource_list" style="overflow: scroll-y; width: 700px; height: 400px; display: none">
    <div class="upload_container">
        <input type="file" multiple="multiple" name="upload" class="file_upload_fallback" id="file_upload_fallback" style="display: block; width: 0px; height: 0px; ">
        <div id="dropzone">Click here to upload (or drag files here)</div>
    </div>

    <table class="resource_table">
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
            @foreach($trust->resources as $resource)
                @include('resources.resources-row', compact('resource'))
            @endforeach
        </tbody>
    </table>
</div>

    <script src="/libs/plupload/plupload.full.js"></script>
    <script src="/libs/plupload/plupload.html5.js"></script>
    <script src="/libs/plupload/plupload.flash.js"></script>
    <link rel="stylesheet" href="/libs/fancybox/jquery.fancybox.css" />
    <script src="/libs/fancybox/jquery.fancybox.pack.js"></script>
    <script src="/js/uploaders.js"></script>
    <script>
        var resourceDestination = null;
        var resourceType = null;
        var resourceFilter = null;
        $(document).ready(function() {

            $('.resource_fancybox').on('click', function() {
                resourceType = $(this).attr('data-dest');
                resourceDestination = $(this).attr('data-resource');
                resourceFilter = $(this).attr('data-filter');

                if (resourceType == 'html') {
                    filterResourceList('image');
                } else {
                    filterResourceList();
                }

                if (resourceFilter == 'image') {
                    filterResourceList('image');
                }

                if (resourceFilter == 'pdf') {
                    filterResourceList('file');
                }
            });

            $('.resource_fancybox').fancybox({
                overlayShow: true,
                hideOnContentClick: false,
                autoScale: false,
            });

            $(document).on('click', '.resource_choose', function(e) {


                e.preventDefault();
                $.fancybox.close();

                if (resourceType == 'html') {
                    var html = '';

                    if ($(this).attr('data-resource-type') == 'image') {
                        html = "<img src='" + $(this).attr('data-resource-url') + "' />";
                    } else {
                        var linkText = $(this).attr('data-resource-caption');

                        if (linkText.length == 0) {
                            linkText = $(this).attr('data-resource-filename');
                        }
                        
                        html = "<a href='" + $(this).attr('data-resource-url') + "'>" + linkText + "</a>";
                    }

                    console.log(html);

                    CKEDITOR.instances[resourceDestination].insertHtml(html);
                } else {
                    var resource_form = $('#' + resourceDestination).closest('.resource_container').find('.resource_form');

                    $('#' + resourceDestination).val($(this).attr('data-resource-id'));
                    resource_form.find('.resource_filename').html( $(this).attr('data-resource-filename') );
                    resource_form.find('.resource_caption').html( $(this).attr('data-resource-caption') );
                    resource_form.find('.resource_remove').show();

                    if ($(this).attr('data-resource-type') == 'image') {
                        // Image
                        resource_form.find('.resource_preview').html('<a class="img" href="' + $(this).attr('data-resource-url') + '"><img src="' + $(this).attr('data-resource-url') + '?type=thumb" width="75" height="50" /></a>');
                    } else {
                        // File
                        resource_form.find('.resource_preview').html('<a style="font-size: 30px" href="' + $(this).attr('data-resource-url') + '"><i class="icon-file"></i></a>');
                    }
                }

                resourceDestination = null;
                resourceType = null;
            });

            $('.resource_preview a.img').fancybox({
                live: true,
                padding: 0
            });

            $('.resource_remove').on('click', function(e) {
                e.preventDefault();

                $(this).hide();

                var resource_form = $(this).closest('.resource_container').find('.resource_form');

                $(this).closest('.resource_container').find('input').val(0);

                resource_form.find('.resource_filename').html("No Resource Selected");
                resource_form.find('.resource_caption').html('');
                resource_form.find('.resource_preview').html('');

            });

            var uploader;

            $( "#upload_progress" ).progressbar({
              value: 0
            }).hide();

            $('.fancybox').fancybox();

            loadResourceUploader('{{ route('resources.process', array($trust->id)) }}', function() {
                
                $.ajax({
                    url: "{{ route('resources.ajax-list', array($trust->id)) }}",
                    success: function(data) {
                        $(".resource_table").html(data);

                        if (resourceFilter == 'image') {
                            filterResourceList('image');
                        }

                        if (resourceFilter == 'pdf') {
                            filterResourceList('file');
                        }
                    }
                });

            });

        });

        function filterResourceList(type) {

            // Reset all first
            $('#resource_list .resource_table tbody tr').show();

            if (type !== undefined) {
                $('#resource_list .resource_table tbody tr[data-type!="' + type + '"]').hide();
            }
        }
    </script>

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

    .resource_table {
        width: 100%;
    }
</style>