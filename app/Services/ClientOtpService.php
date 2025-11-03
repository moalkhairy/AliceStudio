<?php

namespace App\Services;

use App\Mail\ClientOtpMail;
use App\Models\Client;
use App\Models\EmailOtpVerification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ClientOtpService
{
    public function createAndSend(Client $client, int $ttlMinutes = 10): void
    {
        $code = (string)random_int(100000, 999999);
        EmailOtpVerification::create([
            'client_id' => $client->id,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes($ttlMinutes),
            'status' => 'pending',
            'last_sent_at' => now(),
        ]);

        Mail::to($client->email)->send(new ClientOtpMail($code));
    }

    public function verify(Client $client, string $code): bool
    {
        $otp = EmailOtpVerification::where('client_id', $client->id)
            ->where('status', 'pending')
            ->latest()->first();

        if (!$otp) return false;
        if (now()->greaterThan($otp->expires_at)) {
            $otp->update(['status' => 'expired']);
            return false;
        }
        if ($otp->attempts >= $otp->max_attempts) return false;

        $otp->increment('attempts');

        if (!Hash::check($code, $otp->code_hash)) {
            return false;
        }

        $otp->update(['status' => 'verified']);
        return true;
    }
}
