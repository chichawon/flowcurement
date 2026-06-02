<form wire:submit.prevent="save" class="space-y-5">
    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-sm font-semibold text-slate-950">Delivery Details</h3>
        </div>
        <div class="erp-panel-body grid gap-3 lg:grid-cols-4">
            <label class="block">
                <span class="text-sm font-medium text-slate-700">Delivery Receipt No.</span>
                <input type="text" value="{{ $delivery_receipt_no }}" readonly class="mt-1 block h-10 w-full rounded-md border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-950 shadow-sm">
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Received Date</span>
                <input type="date" wire:model.live="received_date" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                @error('received_date') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Received By</span>
                <input type="text" wire:model.blur="received_by" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                @error('received_by') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-sm font-medium text-slate-700">Delivered By</span>
                <input type="text" wire:model.blur="delivered_by" class="mt-1 block h-10 w-full rounded-md border-slate-300 px-3 text-sm shadow-sm erp-focus-ring">
                @error('delivered_by') <span class="mt-1 block text-xs font-medium text-red-600">{{ $message }}</span> @enderror
            </label>
        </div>
    </section>

    <section class="erp-panel">
        <div class="erp-panel-header">
            <h3 class="text-sm font-semibold text-slate-950">Attachments</h3>
        </div>
        <div class="erp-panel-body space-y-4">
            <label class="flex min-h-36 cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-white px-4 py-6 text-center shadow-sm transition hover:border-cyan-400 hover:bg-cyan-50/40">
                <input type="file" wire:model="attachments" multiple accept=".pdf,.jpg,.jpeg,.png,.webp" class="sr-only">
                <span class="grid size-12 place-items-center rounded-full bg-cyan-100 text-cyan-700">
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5h10.5A2.25 2.25 0 0 0 19.5 17.25V6.75A2.25 2.25 0 0 0 17.25 4.5H6.75A2.25 2.25 0 0 0 4.5 6.75v10.5A2.25 2.25 0 0 0 6.75 19.5Z" />
                    </svg>
                </span>
                <span class="mt-3 text-sm font-semibold text-slate-950">Choose attachments</span>
                <span class="mt-1 text-xs text-slate-500">PDF, JPG, PNG, or WEBP up to 5MB each</span>
            </label>
            <div class="flex flex-wrap items-center gap-3 text-xs">
                <span wire:loading wire:target="attachments" class="font-semibold text-cyan-700">Uploading attachments...</span>
                @error('attachments') <span class="font-semibold text-red-600">{{ $message }}</span> @enderror
                @error('attachments.*') <span class="font-semibold text-red-600">{{ $message }}</span> @enderror
            </div>

            @if ($attachments && count($attachments) > 0)
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase text-slate-500">Selected Files</p>
                    <div class="overflow-hidden rounded-md border border-slate-200">
                        <table class="w-full table-fixed border-collapse text-sm">
                            <colgroup>
                                <col class="w-[52%]">
                                <col class="w-[18%]">
                                <col class="w-[18%]">
                                <col class="w-[12%]">
                            </colgroup>
                            <thead class="bg-slate-100 text-xs font-semibold uppercase text-slate-600">
                                <tr>
                                    <th class="border border-slate-200 px-3 py-2 text-left">File Name</th>
                                    <th class="border border-slate-200 px-3 py-2 text-left">Type</th>
                                    <th class="border border-slate-200 px-3 py-2 text-right">Size</th>
                                    <th class="border border-slate-200 px-3 py-2 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                @foreach ($attachments as $index => $file)
                                    @php($ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION)))
                                    <tr>
                                        <td class="border border-slate-200 px-3 py-2">
                                            <p class="truncate font-semibold text-slate-900">{{ $file->getClientOriginalName() }}</p>
                                        </td>
                                        <td class="border border-slate-200 px-3 py-2 text-slate-700">{{ strtoupper($ext) }}</td>
                                        <td class="border border-slate-200 px-3 py-2 text-right text-slate-700">{{ number_format(($file->getSize() ?: 0) / 1024, 1) }} KB</td>
                                        <td class="border border-slate-200 px-3 py-2 text-center">
                                            <button type="button" wire:click="removeSelectedAttachment({{ $index }})" class="text-xs font-semibold text-red-700 hover:text-red-800">Remove</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if ($existingAttachments->isNotEmpty())
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase text-slate-500">Uploaded Attachments</p>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($existingAttachments as $attachment)
                            <div class="rounded-md border border-slate-200 bg-slate-50 p-3">
                                @if ($this->isImage($attachment->file_name))
                                    <img src="{{ $this->attachmentUrl($attachment->file_path) }}" alt="{{ $attachment->file_name }}" class="h-28 w-full rounded border border-slate-200 bg-white object-contain">
                                @else
                                    <div class="grid h-28 place-items-center rounded border border-slate-200 bg-white text-sm font-semibold text-red-600">PDF</div>
                                @endif
                                <p class="mt-2 truncate text-sm font-semibold text-slate-900">{{ $attachment->file_name }}</p>
                                <p class="text-xs text-slate-500">{{ number_format($attachment->file_size / 1024, 1) }} KB</p>
                                <div class="mt-3 flex items-center justify-between gap-2">
                                    <a href="{{ $this->attachmentUrl($attachment->file_path) }}" target="_blank" class="text-xs font-semibold text-cyan-700 hover:text-cyan-800">Preview</a>
                                    <button type="button" wire:click="deleteAttachment({{ $attachment->id }})" class="text-xs font-semibold text-red-700 hover:text-red-800">Delete</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <div class="sticky bottom-0 flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-100/95 py-4 backdrop-blur">
        <a href="{{ route('sales.delivery-receipts.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        <button type="submit" class="rounded-md bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Save Details</button>
    </div>
</form>
