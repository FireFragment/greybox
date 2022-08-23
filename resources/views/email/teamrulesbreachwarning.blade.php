<p>{{ __('messages.hello') }},</p>
<p>{{ __('messages.team.rules.breach.warning.body') }}:</p>
<ul>
    @foreach ($warnings as $warning)
        <li>{{ $warning }}</li>
    @endforeach
</ul>
<p>{{ __('messages.best') }},<br>{{ __('messages.cda') }}</p>