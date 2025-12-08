<?php
/**
 * Configuração de banco de dados via PDO.
 * Ajuste as credenciais conforme ambiente.
 */
return [
    'driver'   => 'mysql',
    'host'     => 'localhost',
    'database' => 'coteli',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
    'collation'=> 'utf8mb4_unicode_ci',
    'options'  => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ],
];
