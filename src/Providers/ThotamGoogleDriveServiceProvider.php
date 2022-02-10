<?php

namespace Thotam\ThotamFileLibrary\Providers;

use Storage;
use Google_Client;
use League\Flysystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Masbug\Flysystem\GoogleDriveAdapter;
use Illuminate\Filesystem\FilesystemAdapter;

class ThotamGoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('google', function($app, $config) {
            $client = new Google_Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);
            $service = new \Google_Service_Drive($client);

            $adapter = new \Masbug\Flysystem\GoogleDriveAdapter(
                $service,
                null,
                [
                    'sanitize_chars' => null,
                ]
            );

            return new FilesystemAdapter(
                new Filesystem($adapter),
                $adapter
            );

            //return new Filesystem($adapter);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
