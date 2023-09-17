<p>{{ __('messages.hello') }},</p>
<p>{{ __('messages.registration.confirmation.before_event_name') }} {{ $eventName }}. {{ __('messages.registration.confirmation.after_event_name') }}</p>
<p>{{ __('messages.registration.confirmation.before_list') }}:</p>
<ul>
    @foreach ($people as $role => $teams)
    <li>{{ $role }}:
        @if (count($teams) > 1)
            <ul>
        @endif
        @foreach ($teams as $name => $people)
            @unless ('emptyTeamName' === $name)
                <li>{{ $name }}:
            @endunless
            @foreach ($people as $person)
                {{ $person }}@unless ($loop->last),@endunless
            @endforeach
            @unless ('emptyTeamName' === $name)
                </li>
            @endunless
        @endforeach
        @if (count($teams) > 1)
            </ul>
        @endif
    </li>
    @endforeach
</ul>
@isset ($invoice)
<p>{{ __('messages.registration.confirmation.before_price') }} {{ number_format($invoice->total, 0) }} {{ $invoice->currency }} {{ __('messages.registration.confirmation.before_date') }} {{ date('d. m. Y', strtotime($invoice->due_on)) }} {{ __('messages.registration.confirmation.after_date') }}</p>
@endisset
<p>{{ __('messages.registration.confirmation.mistake') }}</p>
<p>{{ __('messages.best') }},<br>{{ __('messages.cda') }}</p>