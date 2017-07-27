<p>Hi {{ $user->full_name }},</p>

<p>Your password on {{ Config::get('core.site_name') }} was changed at {{ date('d/m/Y H:i') }}.</p>

<p>If this wasn't you, please contact the site administrator immediately.</p>

<p>{{ Config::get('core.emails.site_signature') }}</p>