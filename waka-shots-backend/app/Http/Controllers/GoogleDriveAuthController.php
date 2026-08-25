<?php

namespace App\Http\Controllers;

use App\Models\GoogleDriveConnection;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Oauth2;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class GoogleDriveAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        $client = $this->client();
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setIncludeGrantedScopes(true);

        return redirect()->away($client->createAuthUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        $adminUrl = url('/admin/google-drive');

        if ($request->filled('error')) {
            return redirect($adminUrl)->with('error', 'Google Drive authorization was cancelled.');
        }

        if (! $request->filled('code')) {
            return redirect($adminUrl)->with('error', 'Google Drive did not return an authorization code.');
        }

        try {
            $token = $this->client()->fetchAccessTokenWithAuthCode($request->string('code')->toString());

            if (isset($token['error']) || blank($token['access_token']) || blank($token['refresh_token'])) {
                throw new \RuntimeException('Google did not return a complete authorization token.');
            }

            $connectedEmail = null;
            try {
                $profileClient = $this->client();
                $profileClient->setAccessToken($token);
                $connectedEmail = (new Oauth2($profileClient))->userinfo->get()->getEmail();
            } catch (Throwable) {
                // The connection remains valid even when profile lookup is unavailable.
            }

            GoogleDriveConnection::query()->delete();
            GoogleDriveConnection::create([
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'],
                'token_expires_at' => isset($token['expires_in'])
                    ? Carbon::now()->addSeconds((int) $token['expires_in'])
                    : null,
                'connected_at' => now(),
                'connected_email' => $connectedEmail,
            ]);

            return redirect($adminUrl)->with('success', 'Google Drive connected successfully.');
        } catch (Throwable $exception) {
            report($exception);

            return redirect($adminUrl)->with('error', 'Google Drive could not be connected. Please try again.');
        }
    }

    private function client(): Client
    {
        $client = new Client();
        $client->setClientId((string) config('services.google.client_id'));
        $client->setClientSecret((string) config('services.google.client_secret'));
        $client->setRedirectUri((string) config('services.google.redirect'));
        $client->setScopes(['https://www.googleapis.com/auth/drive.readonly']);

        return $client;
    }
}
