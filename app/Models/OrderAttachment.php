<?php

namespace App\Models;

use App\Enums\AttachmentType;
use Database\Factories\OrderAttachmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'order_id',
    'type',
    'disk',
    'path',
    'original_name',
    'mime_type',
    'size',
    'uploaded_by',
])]
class OrderAttachment extends Model
{
    /** @use HasFactory<OrderAttachmentFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => AttachmentType::class,
            'size' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function temporaryUrl(): ?string
    {
        return Storage::disk($this->disk)->exists($this->path)
            ? Storage::disk($this->disk)->temporaryUrl($this->path, now()->addMinutes(5))
            : null;
    }
}
