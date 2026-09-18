<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

#[Signature('user:authorize {email : The email address of the operator to authorize} {--name= : Optional display name for the operator}')]
#[Description('Authorize an operator Google account to access the control room.')]
class AuthorizeUser extends Command
{
    public function handle(): int
    {
        $email = strtolower(trim((string) $this->argument('email')));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('The provided email address is invalid.');

            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->first();

        if ($user !== null) {
            if ($user->google_id === $email) {
                $user->forceFill(['google_id' => null])->save();
                $this->info("Operator [{$email}] was already registered. Reset mismatched google_id so Google OAuth can link cleanly.");
            } else {
                $this->info("Operator [{$email}] is already authorized.");
            }

            return self::SUCCESS;
        }

        $name = (string) ($this->option('name') ?: Str::title(explode('@', $email)[0]));

        User::query()->create([
            'name' => $name,
            'email' => $email,
            'password' => Str::random(32),
            'google_id' => null,
        ]);

        $this->info("Operator [{$email}] successfully authorized to sign in with Google.");

        return self::SUCCESS;
    }
}
