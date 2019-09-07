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
@isset($invoice)
<p>{{ __('messages.registration.confirmation.before_price') }} {{ number_format($invoice->total, 0) }} {{ $invoice->currency }} {{ __('messages.registration.confirmation.before_date') }} {{ date('d. m. Y', strtotime($invoice->due_on)) }} {{ __('messages.registration.confirmation.after_date') }}</p>
@endisset
<p>{{ __('messages.registration.confirmation.mistake') }}</p>
<p>{{ __('messages.best') }},<br>{{ __('messages.cda') }}</p>