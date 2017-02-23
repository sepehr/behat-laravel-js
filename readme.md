# Javascript Testing with Behat and Laravel
This is a companion package to [Behat's Laravel Extension](https://github.com/laracasts/behat-laravel-extension/issues/8) 
 that provides utilities to work around some issues and limitations when testing Javascript applications using browser 
 emulators, like [Selenium](https://github.com/minkphp/MinkSelenium2Driver) or 
 [Zombie](https://github.com/minkphp/MinkZombieDriver). 

The workarounds used in this package are heavily inspired by [Laravel Dusk](https://github.com/laravel/dusk) code. Read 
 [this post](https://github.com/laracasts/behat-laravel-extension/issues/8#issuecomment-282050804) if you wish to have 
 more context about the history of the issues.


## Problems and workarounds
If you're here, you probably already know about the extension limitations, but in case you don't; read on.

### tl;dr
[Install](#installation), and use these three traits in your `FeatureContext` and you're done. Remember to not to use
 `DatabaseTransactions` and/or `Migrator` traits.

```php
<?php

use Laracasts\Behat\Context\MigrateRefresh;
use Sepehr\BehatLaravelJs\Concerns\AuthenticateUsers;
use Sepehr\BehatLaravelJs\Concerns\PreserveBehatEnvironment;

class FeatureContext extends MinkContext implements Context
{
    use PreserveBehatEnvironment, AuthenticateUsers, MigrateRefresh;
    
    // ...
}
```

### Environment
> **To alleviate this issue, you need to use the `\Sepehr\BehatLaravelJs\Concerns\PreserveBehatEnvironment` trait in your
`FeatureContext` class.**

Consider this example: Your testing environment is set to use SQLite as the database while your local/production 
 environment use MySQL. When you run a `@javascript` Behat scenario, a browser emulator dispatches a request
 to your Laravel app endpoint. And to your surprise, it meets with another Laravel instance that is using the `.env` 
 file. An instance operating in a different environment: different databases, cache drivers, queues, etc. 

### Database Transactions
> **To alleviate this one, you need to use `Laracasts\Behat\Context\MigrateRefresh` trait in your
`FeatureContext` class instead of using `DatabaseTransactions` and `Migrator` traits.**

The very popular `DatabaseTransactions` trait and its 
 [BLE counterpart](https://github.com/laracasts/Behat-Laravel-Extension/blob/master/src/Context/DatabaseTransactions.php), 
 begin a transaction before a scenario. The transaction will be commited only if there are no exceptions. Then after the 
 scenario, they will rollback it in order to keep the database state intact.

In middle of the process, when a browser emulator dispatches the request to another instance of Laravel, the transaction
 won't be commited and this you will encounter unexpected results.

Consider a scenario, when you first insert a few test users into the database in the testing instance and in the next    
 step, you request a page (to the other instance) to see if their data exist on a page. To your surprise, the data you're
 looking for won't be available.

### Authentication
> **To alleviate this one, you should be using the `\Sepehr\BehatLaravelJs\Concerns\AuthenticateUsers` trait in your
`FeatureContext` class.** It will provide you with helper authentication methods to login, logout and get the current
user data.

Authentication is another problem. In the testing environment you log a user into the system, then you tell Behat to 
 fire a Selenium session and check a protected page. As you might already know, the user won't be logged-in. When you use 
 `Auth::login($userObject)` to log a user in. It logs a user into the current instance, not the one that browser hits.

Available methods are:  

- Login as a user:  
`loginAs($user|$userId, $guard|null)`
- Logout:   
`logout($guard|null)`
- Get current user's data:  
`currentUserInfo($guard|null)`
- Assert that the user is authenticated:  
`assertAuthenticated($guard|null)`
- Assert that the user is authenticated AS:  
`assertAuthenticatedAs($user, $guard|null)`
- Assert that the user is NOT authenticated:  
`assertGuest($guard|null)`

Just as Dusk.

## Installation 
Install the package using composer:

```shell
composer install sepehr/behat-laravel-js
```

Then, in your `AppServiceProvider::boot()`, register the package service provider for testing environments. Please note that
it'd be a **SECURITY RISK** if you enable this in your production environment. See the package service provider to find
out why.

```php
<?php

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->environment('local', 'testing', 'acceptance')) {
            $this->app->register(\Sepehr\BehatLaravelJs\ServiceProvider::class);
        }
    }
    
    // ...
}
```
