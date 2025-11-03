<?php
namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class ClientSocialController extends Controller
{
    public function redirect(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider)
    {
        $social = Socialite::driver($provider)->stateless()->user();

        // Try to match by provider_id or fallback to email
        $client = Client::where([
            'provider' => $provider,
            'provider_id' => $social->getId(),
        ])->first();

        if (!$client && $social->getEmail()) {
            $client = Client::where('email', $social->getEmail())->first();
        }

        if (!$client) {
            $client = Client::create([
                'first_name' => $social->getName() ?: ucfirst($provider) . ' User',
                'last_name' => null,
                'email' => $social->getEmail() ?: $provider . '_' . $social->getId() . '@example.com',
                'password' => bcrypt(str()->random(32)),
                'provider' => $provider,
                'provider_id' => $social->getId(),
                'avatar' => $social->getAvatar(),
                'email_verified_at' => now(),
            ]);
        } else {
            // Ensure provider fields are filled
            $client->update([
                'provider' => $client->provider ?: $provider,
                'provider_id' => $client->provider_id ?: $social->getId(),
                'avatar' => $client->avatar ?: $social->getAvatar(),
            ]);
        }

        Auth::guard('client')->login($client, true);
        return redirect()->intended(route('client.dashboard'));
    }
}
