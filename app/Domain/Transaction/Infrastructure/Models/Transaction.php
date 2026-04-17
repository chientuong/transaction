<?php

namespace App\Domain\Transaction\Infrastructure\Models;

use App\Domain\System\Infrastructure\Models\Setting;
use App\Domain\Transaction\Application\Jobs\ExpireTransactionJob;
use App\Domain\Transaction\Application\Jobs\SendTransactionWebhooksJob;
use App\Domain\Transaction\Domain\Enums\OpsStatusEnum;
use App\Domain\Transaction\Domain\Enums\SyncStatusEnum;
use App\Models\User;
use Database\Factories\TransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Transaction extends Model
{
    use HasFactory;
    use LogsActivity;

    protected static function newFactory()
    {
        return TransactionFactory::new();
    }

    protected $table = 'transactions';

    protected $appends = ['qr_code'];

    protected $fillable = [
        'transaction_code',
        'user_id',
        'prefix_id',
        'amount',
        'bank_account_id',
        'transfer_content',
        'sync_status',
        'ops_status',
        'expires_at',
        'expired_at',
        'confirmed_by',
        'confirmed_at',
        'ops_note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'sync_status' => SyncStatusEnum::class,
            'ops_status' => OpsStatusEnum::class,
            'expires_at' => 'datetime',
            'expired_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_code)) {
                $prefix = $transaction->prefix ? $transaction->prefix->prefix_code : 'TX';
                $transaction->transaction_code = $prefix.'_'.now()->format('YmdHis').'_'.Str::upper(Str::random(6));
            }
            if (empty($transaction->transfer_content)) {
                $transaction->transfer_content = $transaction->transaction_code;
            }
            if (empty($transaction->expires_at)) {
                $ttl = Setting::get('setting_ttl', 15);
                $transaction->expires_at = now()->addMinutes((int) $ttl);
            }
        });

        static::created(function ($transaction) {
            $ttl = Setting::get('setting_ttl', 15);
            ExpireTransactionJob::dispatch($transaction->id)
                ->delay(now()->addMinutes((int) $ttl));
        });

        static::created(function ($transaction) {
            SendTransactionWebhooksJob::dispatch($transaction->id, true, true);
        });

        static::updated(function ($transaction) {
            $syncChanged = $transaction->isDirty('sync_status');
            $opsChanged = $transaction->isDirty('ops_status');

            if ($syncChanged || $opsChanged) {
                SendTransactionWebhooksJob::dispatch(
                    $transaction->id,
                    $syncChanged,
                    $opsChanged
                );
            }
        });
    }

    public function prefix(): BelongsTo
    {
        return $this->belongsTo(PaymentPrefix::class, 'prefix_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'subject');
    }

    public function webhookLogs(): HasMany
    {
        return $this->hasMany(WebhookLog::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->dontLogEmptyChanges();
    }

    public function getQrCodeAttribute(): ?string
    {
        if (! $this->bankAccount) {
            return null;
        }

        return "https://qr.sepay.vn/img?acc={$this->bankAccount->account_number}&bank={$this->bankAccount->bank_code}&amount={$this->amount}&des={$this->transaction_code}";
    }
}
