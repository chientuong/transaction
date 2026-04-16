<?php

namespace App\Domain\Transaction\Infrastructure\Models;

use App\Models\User;
use Database\Factories\BankAccountFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class BankAccount extends Model
{
    use HasFactory;
    use LogsActivity;

    protected static function newFactory()
    {
        return BankAccountFactory::new();
    }

    protected $table = 'bank_accounts';

    protected $fillable = [
        'bank_code',
        'bank_branch',
        'account_number',
        'account_holder',
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

        static::creating(function ($account) {
            if (Auth::check() && empty($account->created_by)) {
                $account->created_by = Auth::id();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
