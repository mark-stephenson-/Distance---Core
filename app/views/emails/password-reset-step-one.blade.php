<p>Hi {{ $user->full_name }},</p>

<p>You (or someone pretending to be you), has requested to reset your password on {{ Config::get('core.site_name') }}. To continue this process, simply click the link below.</p>

<a href="{{ URL::to('/forgot-password/' . $user->id . '/'. $resetCode) }}">{{ URL::to('/forgot-password/' . $user->id . '/' . $resetCode) }}</a>

<p>If it wasn't you, simply ignore this email.</p>

<p>{{ Config::get('core.emails.site_signature') }}</p>