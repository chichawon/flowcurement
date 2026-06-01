@props([
    'label',
    'name',
    'required' => false,
])

<label class="block">
    <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
    <textarea
        name="{{ $name }}"
        rows="4"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-slate-300 px-3 py-2 text-sm shadow-sm erp-focus-ring']) }}
    >{{ $slot }}</textarea>
    @error($name)
        <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span>
    @enderror
</label>
