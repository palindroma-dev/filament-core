@php
    $id = $getId();
@endphp
<div class="fi-ac-select-action" x-data="{
    changeLanguage(locale) {
        const selectElement = document.getElementById('{{ $id }}');
        selectElement.value = locale;
        selectElement.dispatchEvent(new Event('change'));
    }
}">
    <x-filament::tabs :contained="true" :label="$getLabel()">
      @foreach ($getOptions() as $value => $label)
        <x-filament::tabs.item
          :alpine-active="'$wire.activeLocale === \'' . $value . '\''"
          x-on:click="changeLanguage('{{ $value }}')"
        >
          {{ $value. ' ' . (config('app.locale_labels')[$value] ?? $label) }}
        </x-filament::tabs.item>
      @endforeach
    </x-filament::tabs>

    <div class="fi-ac-select-action" style="position: absolute; visibility: hidden">
        <x-filament::input.wrapper>
            <x-filament::input.select
              id="{{ $id }}"
              :wire:model.live="$getName()"
            >
                @foreach ($getOptions() as $value => $label)
                    <option value="{{ $value }}">
                        {{ $label }}
                    </option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </div>
</div>
