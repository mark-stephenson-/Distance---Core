<?php
	$identifier = uniqid();
	$language = 'en';

    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
?>

<div id="{{ $identifier }}" class="span8" style="margin-left: 0px">
    <!-- Contains i18n_html key -->
    {{ Form::hidden('nodetype['.$column->name.']', $value) }}
    {{ Form::select("language", Config::get("languages.list"), 'en', array("class" => "child-select", "style" => "margin:0 4px 4px 0")) }}
    <i class="icon-globe" data-toggle="tooltip" title="Toggle localisation of this category."></i>
    {{ Form::textarea(null, $value, array('class' => 'html-editor', 'id' => 'input_'.$column->name, 'lang' => $language, 'style' => 'display:none')) }}
	@foreach (Config::get("languages.list") as $lang => $language)
		<?php
			if (isset($data)) {
				$i18nHtml = I18nHtml::whereKey($value)->whereLang($lang)->get()->first();
				$translation = $i18nHtml ? $i18nHtml->value : "";
			} else { $translation = ""; }
		?>
		{{ Form::textarea('translation['.$column->name.']['.$lang.']', $translation, array('style' => 'display:none')) }}
	@endforeach
</div>

@if (!isset($column->catalogue) or !isset($column->catalogue->{CORE_COLLECTION_ID}))
    
    <p>A catalogue has not yet been assigned to this field - please contact an adminstrator</p>

@else

<?php $catalogue = Catalogue::find($column->catalogue->{CORE_COLLECTION_ID}); ?>

<a href="#{{ $column->name }}-resource_window" data-toggle="modal" style="display: none" data-dest="html" id="input_{{ $column->name }}_resource_link" data-resource="input_{{ $column->name }}" class="resource_fancybox">Choose One</a>

@if ($column->description)
    <span class="help-block">{{ $column->description }}</span>
@endif

<div class="modal hide fade" id="{{ $column->name }}-resource_window">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Resources <small>Catalogue: {{ $catalogue->name }}</small></h3>
    </div>

    <div class="modal-body">
        <table class="table" style="table-layout: fixed;">
            <thead>
                <tr>
                    <th></th>
                    <th style="width: 420px !important; max-width: 420px;">Filename</th>
                    <th>Sync</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach( $catalogue->resources as $resource )
                    <tr>
                        <td>
                            @if ( $resource->isImage() )
                                <img src="{{ $resource->path() }}?type=view" alt="" style="max-width: 24px; max-height: 24px;" />
                            @else
                                <i class="icon-file"></i>
                            @endif
                        </td>
                        <td>
                            {{ substr($resource->filename, 0, 50) }}
                            @if (strlen($resource->filename) >= 50)
                                &hellip;
                            @endif
                        </td>
                        <td>
                            @if ( $resource->sync )
                                <i class="icon-ok"></i>
                            @else
                                <i class="icon-remove"></i>
                            @endif
                        </td>
                        <td>
                            <a href="#" data-id="{{ $resource->id }}" data-filename="{{ $resource->filename }}" @if ( $resource->isImage() ) data-image="true" @endif>Use</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $("#{{ $column->name }}-resource_window").on('shown', function() {
        $("#{{ $column->name }}-resource_window a").click( function(e) {
            e.preventDefault();
            
            var html = '';

            if ( $(this).attr('data-image') == "true") {
                html = "<img src='" + $(this).attr('data-filename') + "' />";
            } else {                
                html = "<a href='" + $(this).attr('data-filename') + "'>" + $(this).attr('data-filename') + "</a>";
            }

            $("#{{ $identifier }} .html-editor").ckeditor().ckeditorGet().insertHtml(html);
            $("#{{ $column->name }}-resource_window").modal('hide');
        });
    });
</script>

@endif

<script>
    $(function() {
        
        var editor = $("#{{ $identifier }} .html-editor").ckeditor().ckeditorGet();
        var lang = $("#{{ $identifier }} .html-editor").attr('lang');
        var html = editor.getData();
        
        editor.config.baseHref = "{{ URL::to('file') }}/{{ $collection->id }}/" + lang + "/";
        editor.setData($("#{{ $identifier }} [name='translation[{{ $column->name }}][" + lang + "]']").text());
        editor.on('change', function(){
            $("#{{ $identifier }} [name='translation[{{ $column->name }}][" + $('#{{ $identifier }} select[name=language]').val() + "]']").text(editor.getData());
        });
        
        $('#{{ $identifier }} select[name=language].child-select').change(function() {
            
            html = editor.getData();
            lang = $("#{{ $identifier }} .html-editor").attr('lang');
            
            $("#{{ $identifier }} [name='translation[{{ $column->name }}][" + lang + "]']").text(html);
            $("#{{ $identifier }} .lang").text("[" + $(this).val().toUpperCase() + "]")
            $("#{{ $identifier }} .html-editor").attr('lang', $(this).val());
            
            editor.setData($("#{{ $identifier }} [name='translation[{{ $column->name }}][" + $(this).val() + "]']").text());
            
        }).change();
    });
</script>