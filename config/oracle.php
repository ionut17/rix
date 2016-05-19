<?php

return [
  'oracle' => [
    'driver'        => 'oracle',
    'host'          => 'localhost',
    'port'          => 1521,
    'database'      => 'xe',
    'username'      => 'rix',
    'password'      => 'rix',
    'charset'       => 'AL32UTF8',
    'prefix'        => env('DB_PREFIX', ''),
    'prefix_schema' => env('DB_SCHEMA_PREFIX', ''),
  ],
];
