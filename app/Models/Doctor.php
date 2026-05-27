<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'user_id',
        'name',
        'slug',
        'specialty',
        'bio',
        'qualification',
        'experience_years',
        'consultation_fee',
        'online_fee',
        'offline_fee',
        'online_available',
        'offline_available',
        'clinic_address',
        'photo_path',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'experience_years' => 'integer',
        'consultation_fee' => 'decimal:2',
        'online_fee' => 'decimal:2',
        'offline_fee' => 'decimal:2',
        'online_available' => 'boolean',
        'offline_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Doctor $doctor): void {
            if (blank($doctor->slug) && filled($doctor->name)) {
                $doctor->slug = Str::slug($doctor->name);
            }
        });
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
