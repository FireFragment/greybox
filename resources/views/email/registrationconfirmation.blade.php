<p>{{ __('messages.hello') }},</p>
<p>{{ __('messages.registration.confirmation.before_event_name') }} {{ $eventName }}. {{ __('messages.registration.confirmation.after_event_name') }}</p>
<p>{{ __('messages.registration.confirmation.before_list') }}:</p>
<ul>
    @foreach ($people as $roles => $group)
    <li>{{ $roles }}:
        @foreach ($group as $team)
            @foreach ($team as $person)
                {{ $person }},
            @endforeach
        @endforeach
    </li>
    @endforeach
</ul>
<p>{{ __('messages.registration.confirmation.after_list') }}</p>
<p>{{ __('messages.best') }},<br>{{ __('messages.cda') }}</p>