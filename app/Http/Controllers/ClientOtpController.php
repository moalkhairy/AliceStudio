<?php

namespace App\Http\Controllers;

use App\Services\ClientOtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientOtpController extends Controller
{
    public function showVerify()
    {
        return view('client.auth.verify');
    }

    public function send(Request $request, ClientOtpService $service)
    {
        $client = Auth::guard('client')->user();
        $service->createAndSend($client);
        return back()->with('status', 'Verification code sent to your email.');
    }

    public function verify(Request $request, ClientOtpService $service)
    {
        $request->validate(['code' => 'required|string']);
        $client = Auth::guard('client')->user();

        if (!$service->verify($client, $request->code)) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }

        // mark verified + award signup bonus if not yet
        DB::transaction(function () use ($client) {
            if (!$client->email_verified_at) {
                $client->email_verified_at = now();
            }
            if (!$client->signup_bonus_awarded_at) {
                // +3 coins signup bonus
                $client->coins += 3;
                $client->signup_bonus_awarded_at = now();

                $client->save();

                $client->walletTransactions()->create([
                    'type' => 'signup_bonus',
                    'amount' => +3,
                    'balance_after' => $client->coins,
                    'meta' => ['reason' => 'Signup bonus after email verification'],
                ]);
            } else {
                $client->save();
            }
        });

        return redirect()->route('client.dashboard')->with('status', 'Email verified!');
    }
}
