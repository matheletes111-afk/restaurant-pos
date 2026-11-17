<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffDocument extends Model
{
    use HasFactory;

    protected $table = 'staff_documents';

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'title',
        'description',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(RestaurantMaster::class, 'restaurant_id');
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    public function getFileUrlAttribute(): string
    {
        if (str_starts_with($this->file_path, 'uploads/')) {
            return asset($this->file_path);
        }
        return asset('uploads/staff_documents/' . $this->file_path);
    }

    public function getIconClassAttribute(): string
    {
        $ext = strtolower($this->file_type ?? '');
        if (in_array($ext, ['pdf'])) {
            return 'fas fa-file-pdf text-danger';
        } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            return 'fas fa-file-image text-primary';
        } elseif (in_array($ext, ['doc', 'docx'])) {
            return 'fas fa-file-word text-info';
        }
        return 'fas fa-file-alt text-secondary';
    }

    public function getIsImageAttribute(): bool
    {
        $ext = strtolower($this->file_type ?? '');
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    }
}
