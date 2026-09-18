<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('device:issue-token {email} {name} {--ability=* : Token ability, repeatable}')]
#[Description('Issue a Sanctum token for a device client.')]
class IssueDeviceToken extends Command
{
    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error('No user exists with that email address.');

            return self::FAILURE;
        }

        $token = DB::transaction(function () use ($user): string {
            $device = $user->devices()->create([
                'name' => $this->argument('name'),
            ]);
            $newToken = $user->createToken(
                $device->name,
                $this->option('ability') ?: ['device:write', 'device:read'],
            );
            $device->update(['personal_access_token_id' => $newToken->accessToken->id]);

            return $newToken->plainTextToken;
        });

        $this->info('Token created. Store it securely; it will not be shown again.');
        $this->line($token);

        return self::SUCCESS;
    }
}
