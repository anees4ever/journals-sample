<?php

include(ROOT_DIR."/thirdparty/vendor/autoload.php");

$dotenv = Dotenv\Dotenv::createImmutable(ROOT_DIR);
$dotenv->safeLoad();

$config= [

    //Server Timezone for PHP and MySQL
    'time_zone' => env("TIMEZONE", "Asia/Kolkata"),

    //Relative path to then index.php file(this) from the top domain
    "documentRoot" => env("DOCUMENT_ROOT_PATH", "/"),

    //default controller name if not specified
    "defaultController" => env("DEFAULT_CONTROLLER", "journals"),

    //database configurations
    "mysql_host" => env("MYSQL_HOST", "localhost"),
    "mysql_database" => env("MYSQL_DATABASE", "orionedge_hexa_erp"),
    "mysql_username" => env("MYSQL_USERNAME", "root"),
    "mysql_password" => env("MYSQL_PASSWORD", ""),
];

function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}