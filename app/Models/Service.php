<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    protected $fillable = [
        'service_name',
        'price',
        'description',
        'image',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function imageUrl(): ?string
    {
        if (! $this->image || ! preg_match('/\A(?:services\/)?[a-zA-Z0-9_-]+\.(?:jpg|jpeg|png|webp)\z/i', $this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'services/')) {
            return Storage::disk('public')->exists($this->image)
                ? asset('storage/'.$this->image)
                : null;
        }

        return File::exists(public_path('uploads/services/'.$this->image))
            ? asset('uploads/services/'.$this->image)
            : null;
    }

    public function deleteUnusedImage(?string $image): void
    {
        if (! $image || ! preg_match('/\A(?:services\/)?[a-zA-Z0-9_-]+\.(?:jpg|jpeg|png|webp)\z/i', $image)
            || self::where('image', $image)->exists()) {
            return;
        }

        if (str_starts_with($image, 'services/')) {
            Storage::disk('public')->delete($image);
        } else {
            File::delete(public_path('uploads/services/'.$image));
        }
    }
}
