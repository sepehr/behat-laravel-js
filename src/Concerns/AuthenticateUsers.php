<?php

namespace Sepehr\BehatLaravelJs\Concerns;

use PHPUnit_Framework_Assert as PHPUnit;

trait AuthenticateUsers
{
    /**
     * Log into the application using a given user ID or email.
     *
     * @param  object|string  $userId
     * @param  string         $guard
     *
     * @return $this
     */
    public function loginAs($userId, $guard = null)
    {
        $userId = method_exists($userId, 'getKey') ? $userId->getKey() : $userId;

        $this->visitPath(rtrim("/_behat/login/$userId/$guard", '/'));

        return $this;
    }

    /**
     * Log out of the application.
     *
     * @param  string  $guard
     *
     * @return $this
     */
    public function logout($guard = null)
    {
        $this->visitPath(rtrim("/_behat/logout/$guard", '/'));

        return $this;
    }

    /**
     * Get the ID and the class name of the authenticated user.
     *
     * @param  string|null  $guard
     *
     * @return array
     */
    protected function currentUserInfo($guard = null)
    {
        $response = $this->visitWithResponse("/_behat/user/$guard");

        return json_decode(strip_tags($response), true);
    }

    /**
     * Assert that the user is authenticated.
     *
     * @param  string|null  $guard
     *
     * @return $this
     */
    public function assertAuthenticated($guard = null)
    {
        PHPUnit::assertNotEmpty($this->currentUserInfo($guard), 'The user is not authenticated.');

        return $this;
    }

    /**
     * Assert that the user is not authenticated.
     *
     * @param  string|null  $guard
     *
     * @return $this
     */
    public function assertGuest($guard = null)
    {
        PHPUnit::assertEmpty(
            $this->currentUserInfo($guard),
            'The user is unexpectedly authenticated.'
        );

        return $this;
    }

    /**
     * Assert that the user is authenticated as the given user.
     *
     * @param  $user
     * @param  string|null  $guard
     *
     * @return $this
     */
    public function assertAuthenticatedAs($user, $guard = null)
    {
        $expected = [
            'className' => get_class($user),
            'id'        => $user->getAuthIdentifier(),
        ];

        PHPUnit::assertSame(
            $expected,
            $this->currentUserInfo($guard),
            'The currently authenticated user is not who was expected.'
        );

        return $this;
    }

    /**
     * Visit theh page and return the response.
     *
     * @param  string  $path
     * @param  string  $sessionName
     *
     * @return string
     */
    public function visitWithResponse($path, $sessionName = null)
    {
        $this->visit($path, $sessionName);

        return $this->getSession($sessionName)->getDriver()->getContent();
    }
}
