<?php
/**
 * Bootstrap
 * Included at the top of every entry-point script.
 * Loads config, sets up simple autoloading for models/core classes,
 * and starts the session.
 */
define('ROOT_PATH', dirname(__DIR__));

require_once ROOT_PATH . '/config/config.php';

spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . '/core/' . $class . '.php',
        ROOT_PATH . '/models/' . $class . '.php',
        ROOT_PATH . '/controllers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

Session::start();
