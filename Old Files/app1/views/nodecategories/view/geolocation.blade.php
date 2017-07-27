<p>{{ $data->{$column->name} }}</p>

<img src="http://maps.googleapis.com/maps/api/staticmap?center={{ $data->{$column->name} }}&amp;zoom=13&amp;size=400x150&amp;maptype=roadmap
&amp;markers=color:red%7C{{ $data->{$column->name} }}&amp;sensor=false" width="400" height="150" />