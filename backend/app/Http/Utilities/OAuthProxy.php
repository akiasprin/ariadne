<?php

namespace App\Http\Utilities;


use Illuminate\Support\Facades\Cookie;
use Optimus\ApiConsumer\Facade\ApiConsumer;

trait OAuthProxy
{
    /**
     * Proxy a request to the OAuth server.
     *
     * @param string $grantType what type of grant type should be proxied
     * @param array $data the data to send to the server
     */
    public function proxy($grantType, array $data = [])
    {
        $data = array_merge($data, [
            'client_id'     => env('PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSWORD_CLIENT_SECRET'),
            'grant_type'    => $grantType
        ]);

        $response = ApiConsumer::post('/oauth/token', $data);

        if (!$response->isSuccessful()) {
            return [];
        }
        $data = json_decode($response->getContent());

        // Create a refresh token cookie
        Cookie::queue(
            'refreshToken',
            $data->refresh_token,
            864000, // 10 days
            null,
            null,
            false,
            true // HttpOnly
        );

        return [
            'access_token' => $data->access_token,
            'expires_in' => $data->expires_in
        ];
    }
}