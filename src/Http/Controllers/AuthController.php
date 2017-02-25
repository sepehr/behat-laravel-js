<?php

namespace Sepehr\BehatLaravelJs\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AuthController
{
    /**
     * Retrieve the authenticated user identifier and class name.
     *
     * @param  string|null  $guard
     *
     * @return array
     */
    public function user($guard = null)
    {
        $user = Auth::guard($guard)->user();

        return $user ? [
            'className' => get_class($user),
            'id'        => $user->getAuthIdentifier(),
        ] : [];
    }

    /**
     * Login using the given user ID / email.
     *
     * @param  string       $userId
     * @param  string|null  $guard
     *
     * @return void
     */
    public function login($userId, $guard = null)
    {
        $model = $this->modelForGuard(
            $guard = $guard ?: config('auth.defaults.guard')
        );

        $user = str_contains($userId, '@')
            ? (new $model)->where('email', $userId)->first()
            : (new $model)->find($userId);

        Auth::guard($guard)->login($user);
    }

    /**
     * Log the user out of the application.
     *
     * @param  string|null  $guard
     *
     * @return void
     */
    public function logout($guard = null)
    {
        Auth::guard($guard ?: config('auth.defaults.guard'))->logout();
    }

    /**
     * Get the model for the given guard.
     *
     * @param  string  $guard
     *
     * @return string
     */
    protected function modelForGuard($guard)
    {
        $provider = config("auth.guards.$guard.provider");

        return config("auth.providers.$provider.model");
    }
}
