@props([
    'label',
    'name',
    'options' => [],
    'required' => false,
])

<label class="block">
    <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
    <select
        name="{{ $name }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring']) }}
    >
        {{ $slot->isEmpty() ? '' : $slot }}
        @foreach ($options as $value => $labelText)
            <option value="{{ $value }}">{{ $labelText }}</option>
        @endforeach
    </select>
    @error($name)
        <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
    @enderror
</label>
