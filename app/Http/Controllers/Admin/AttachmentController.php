<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AttachmentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAttachmentRequest;
use App\Models\Order;
use App\Services\AttachmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class AttachmentController extends Controller
{
    public function __construct(private readonly AttachmentService $attachments) {}

    public function store(StoreAttachmentRequest $request, Order $order): RedirectResponse
    {
        Gate::authorize('update', $order);
        $validated = $request->validated();
        $this->attachments->store(
            $order,
            AttachmentType::from($validated['type']),
            $request->file('file'),
            $request->user(),
        );

        return back()->with('success', 'Lampiran berhasil disimpan.');
    }
}
