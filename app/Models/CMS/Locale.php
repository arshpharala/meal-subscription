<?php

namespace App\Models\CMS;

use App\Enums\TextDirection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Locale extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name',
        'direction',
        'logo',
    ];

    protected $casts = [
        'direction' => TextDirection::class, // This will cast string <-> enum
    ];


    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['logo_url'];

    /**
     * Get the logo full path of the locale.
     *
     * @return string|null
     */
    public function getLogoUrlAttribute()
    {
        return $this->logo_url();
    }


    // Helper: check if RTL
    public function isRtl(): bool
    {
        return $this->direction === TextDirection::RTL;
    }

    // Helper: check if LTR
    public function isLtr(): bool
    {
        return $this->direction === TextDirection::LTR;
    }

    /**
     * Get the logo full path of the locale.
     *
     * @return string|void
     */
    public function logo_url()
    {
        if (empty($this->logo)) {
            return;
        }

        return Storage::url($this->logo);
    }
}
