@php($currentDate = now())

<div class="lavea-page-date">
    <strong>{{ $currentDate->format('F d, Y') }}</strong>
    {{ $currentDate->format('l') }}
</div>
