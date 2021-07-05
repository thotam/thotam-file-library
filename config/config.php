<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'youtube' => [
        'chunkSize' => env('MAIN_YOUTUBE_CHUNK_SIZE', 10 * 1024 * 1024),
        'refreshToken' => env('MAIN_YOUTUBE_REFRESH_TOKEN', null),
        'clientSecret' => env('MAIN_YOUTUBE_CLIENT_SECRET', null),
        'clientId' => env('MAIN_YOUTUBE_CLIENT_ID', null),
    ]
];
