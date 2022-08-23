<p>{{ __('messages.hello') }},</p>
<p>{{ __('messages.team.rules.breach.warning.body') }}:</p>
<ul>
    @foreach ($data as $person)
        <li>{{ $person->name }} {{ $person->surname }}</li>
    @endforeach
</ul>
<p>{{ __('messages.best') }},<br>{{ __('messages.cda') }}</p>