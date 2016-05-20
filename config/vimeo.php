<?php

/*
 * This file is part of Laravel Vimeo.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Vimeo Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [

        'main' => [
            'client_id' => '9d9c308f07fe242b0d8e5f6c28c9aa5e21f4a269',
            'client_secret' => '8yoFl8zPuGh2r1GpJFIaXhVYAUGVoMDeDGDGRNix4f9f7By2gexx1KdhQrgOYHb5rSN9ebFmjhkbZOOd6iRg3ngGeFV7eyN9v5Ej O9TN5Ui2kiWdHelLM9X8se5LsPLp',
            'access_token' => '61e8bb868b00fabc8020b67f7269dd0a',
        ],

        'alternative' => [
            'client_id' => 'your-client-id',
            'client_secret' => 'your-client-secret',
            'access_token' => null,
        ],

    ],

];
