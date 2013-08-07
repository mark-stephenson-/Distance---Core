<?php
    $id = uniqid();

    if (!isset($data)) {
        $value = @$column->default;
    } else {
        $value = @$data->{$column->name};
    }
?>

<div id="{{ $column->name }}" class="input-append span8" style="margin-left: 0px">
    {{ Form::text('nodetype['. $column->name .']', Input::old('nodetype.' . $column->name, $value), ['class' => 'span8 validate-geolocation', 'id' => $id . '-latlng']) }}

    <a class="btn add-on" href="#{{ $id }}-location-picker" data-toggle="modal">
        <i class="icon-map-marker"></i>
    </a>
</div>

<div id="{{ $id }}-location-picker" class="modal hide fade" tabindex="-1" role="dialog">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3>Location Picker</h3>
    </div>

    <div class="modal-body">
        <div id="{{ $id }}-canvas" style="height: 350px; width: 525px; display: block"></div>
    </div>

    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
    </div>
</div>

    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
    <script>
        $('#{{ $id }}-location-picker').on('shown', function() {
            var map;
            var latlng;

            var values = $('#{{ $id }}-latlng').val().split(',');
            
            if ( $('#{{ $id }}-latlng').val() ) {
                latlng = new google.maps.LatLng(parseFloat(values[0]), parseFloat(values[1]));
            } else {
                latlng = new google.maps.LatLng(54.539551,-4.533234);
            }

            var mapOptions = {
                zoom: 5,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };

            map = new google.maps.Map(document.getElementById('{{ $id }}-canvas'), mapOptions);

            var marker = new google.maps.Marker({
                position: latlng,
                map: map,
                draggable: true
            });

            google.maps.event.addListener(marker, 'dragend', function() {
                var position = String(marker.getPosition()).replace('(', '').replace(')', '');
                $('#{{ $id }}-latlng').val( position );
              });
        });
    </script>



@if ($column->description)
    <span class="help-block">{{ $column->description }}</span>
@endif