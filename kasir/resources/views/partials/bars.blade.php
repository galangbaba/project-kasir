@use('App\Support\Format')
@php $max = max(1, $items->max('omzet')); @endphp
<div class="bars" role="img" aria-label="Grafik pendapatan harian">
    @foreach ($items as $it)
        <div class="bar">
            <span class="v">{{ Format::jt($it['omzet']) }}</span>
            <div class="track"><i data-h="{{ $it['omzet'] > 0 ? max(3, round($it['omzet'] / $max * 100)) : 0 }}"></i></div>
            <span class="l">{{ $it['label'] }}</span>
        </div>
    @endforeach
</div>
