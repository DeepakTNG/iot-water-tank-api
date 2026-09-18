<?php

namespace App\Models;

use Database\Factories\DeviceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Sanctum\PersonalAccessToken;

#[Fillable(['user_id', 'name', 'personal_access_token_id', 'is_active'])]
class Device extends Model
{
    /** @use HasFactory<DeviceFactory> */
    use HasFactory;

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<DeviceMessage, $this> */
    public function messages(): HasMany
    {
        return $this->hasMany(DeviceMessage::class);
    }

    /** @return HasOne<DeviceMessage, $this> */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(DeviceMessage::class)->latestOfMany('received_at');
    }

    /** @return BelongsTo<PersonalAccessToken, $this> */
    public function personalAccessToken(): BelongsTo
    {
        return $this->belongsTo(PersonalAccessToken::class);
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
