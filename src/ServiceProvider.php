<?php

namespace Sepehr\BehatLaravelJs;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServideProvider;

class ServiceProvider extends BaseServideProvider
{
    /**
     * Register behat authentication routes.
     *
     * @return void
     */
    public function boot()
    {
        Route::get('/_behat/login/{userId}/{guard?}', [
            'middleware' => 'web',
            'uses'       => 'Sepehr\BehatLaravelJs\Http\Controllers\AuthController@login',
        ]);

        Route::get('/_behat/logout/{guard?}', [
            'middleware' => 'web',
            'uses'       => 'Sepehr\BehatLaravelJs\Http\Controllers\AuthController@logout',
        ]);

        Route::get('/_behat/user/{guard?}', [
            'middleware' => 'web',
            'uses'       => 'Sepehr\BehatLaravelJs\Http\Controllers\AuthController@user',
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
