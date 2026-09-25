<?php

namespace App\Services;

use App\Enums\AttachmentType;
use App\Models\Order;
use App\Models\OrderAttachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttachmentService
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function store(
        Order $order,
        AttachmentType $type,
        UploadedFile $file,
        User $actor,
    ): OrderAttachment {
        if ($file->getSize() > ((int) config('laundryku.uploads.max_kilobytes') * 1024)) {
            throw ValidationException::withMessages([
                'file' => ['Ukuran file terlalu besar.'],
            ]);
        }

        $disk = (string) config('laundryku.uploads.disk');

        return DB::transaction(function () use ($order, $type, $file, $actor, $disk): OrderAttachment {
            $path = $file->store('order-attachments/'.$order->id, $disk);
            $attachment = $order->attachments()->create([
                'type' => $type,
                'disk' => $disk,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
                'size' => $file->getSize(),
                'uploaded_by' => $actor->getKey(),
            ]);

            $this->auditLogger->record($order, 'attachment.uploaded', $actor, [], [
                'attachment_id' => $attachment->id,
                'type' => $type->value,
            ]);

            return $attachment;
        });
    }
}
