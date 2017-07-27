<?php
    $column_name = $data->{$column->name};
?>

@if ($column_name)
	<ul>
		@foreach (I18nString::whereKey($column_name)->get() as $translation)
			<li>
                <span>[{{ strtoupper($translation->lang) }}]</span>
				<span lang="{{ $translation->lang }}">{{ $translation->value }}</span>
			</li>
		@endforeach
	</ul>
@else
    N/A
@endif
