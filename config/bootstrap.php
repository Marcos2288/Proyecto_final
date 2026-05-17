<?php

require_once __DIR__ . '/database.php';

define('BASE_PATH', dirname(__DIR__));
define('CORE_PATH', BASE_PATH . '/core');
define('SERVICES_PATH', BASE_PATH . '/services');
define('MODELS_PATH', BASE_PATH . '/models');

spl_autoload_register(function ($class) {
    $paths = [
        CORE_PATH . '/' . $class . '.php',
        SERVICES_PATH . '/' . $class . '.php',
        MODELS_PATH . '/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getAuthService(): AuthService
{
    static $instance = null;
    if ($instance === null) {
        $instance = new AuthService();
    }
    return $instance;
}

function getCartService(): CartService
{
    static $instance = null;
    if ($instance === null) {
        $instance = new CartService();
    }
    return $instance;
}

function getContentService(): ContentService
{
    static $instance = null;
    if ($instance === null) {
        $instance = new ContentService();
    }
    return $instance;
}

function getProductService(): ProductService
{
    static $instance = null;
    if ($instance === null) {
        $instance = new ProductService();
    }
    return $instance;
}

function getRatingService(): RatingService
{
    static $instance = null;
    if ($instance === null) {
        $instance = new RatingService();
    }
    return $instance;
}

function getForumService(): ForumService
{
    static $instance = null;
    if ($instance === null) {
        $instance = new ForumService();
    }
    return $instance;
}
