<?php

namespace App\Domain\Transaction\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

use Database\Factories\PaymentPrefixFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentPrefix extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return PaymentPrefixFactory::new();
    }
    protected $table = 'payment_prefixes';

    protected $fillable = [
        'name',
        'prefix_code',
        'description',
        'is_active',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($prefix) {
            if (Auth::check() && empty($prefix->created_by)) {
                $prefix->created_by = Auth::id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
