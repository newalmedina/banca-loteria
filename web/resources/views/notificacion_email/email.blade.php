@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {!! $greeting !!}
@else
@if ($level == 'error')
# Whoops!
@else
# {{ trans("general/email_lang.hola") }}
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{!! $line !!}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
            $color = 'green';
            break;
        case 'error':
            $color = 'red';
            break;
        default:
            $color = 'blue';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{!! $line !!}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{!! $salutation !!}
@else
{{ trans("general/email_lang.saludo") }},<br>{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)
@component('mail::subcopy')
{{ trans("general/email_lang.sucopy_01") }} "{{ $actionText }}" {{ trans("general/email_lang.sucopy_02") }}
[{{ $actionUrl }}]({{ $actionUrl }})
@endcomponent
@endisset
@endcomponent
