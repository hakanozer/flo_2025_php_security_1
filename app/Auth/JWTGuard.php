<?php

namespace App\Auth;

use App\Helpers\JWTHelper;
use App\Models\User;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class JWTGuard implements Guard
{
    use GuardHelpers;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Create a new authentication guard.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider  $provider
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $token = $this->getTokenForRequest();

        if (empty($token)) {
            return null;
        }

        $payload = JWTHelper::decode($token);

        if ($payload === null) {
            return null;
        }

        $id = $payload['sub'] ?? null;

        if ($id === null) {
            return null;
        }

        $user = User::find($id);

        if ($user && $user->api_token === $token) {
            return $this->user = $user;
        }

        return null;
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if (empty($credentials['email']) || empty($credentials['password'])) {
            return false;
        }

        $user = $this->provider->retrieveByCredentials($credentials);

        if (!$user) {
            return false;
        }

        return $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * Get the token for the current request.
     *
     * @return string|null
     */
    protected function getTokenForRequest()
    {
        $token = $this->request->bearerToken();

        if (empty($token)) {
            $token = $this->request->input('token');
        }

        return $token;
    }
}
