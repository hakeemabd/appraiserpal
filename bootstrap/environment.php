<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 12/22/15
 * Time: 2:35 PM
 */
$env = $app->detectEnvironment(function () {
    $envDir = __DIR__ . '/../';
    Dotenv::load($envDir, '.env'); //load the main environment
    Dotenv::required(['CUSTOMER_HOST', 'ADMIN_HOST', 'WORKER_HOST']);
    if (php_sapi_name() === 'cli') {
        return getenv('APP_ENV') ?: 'production';
    }
    switch ($_SERVER['HTTP_HOST']) {
        case env('CUSTOMER_HOST'):
            $envFile = 'customer.env';
            break;
        case env('ADMIN_HOST'):
            $envFile = 'admin.env';
            break;
        case env('WORKER_HOST'):
            $envFile = 'worker.env';
            break;
        default:
            throw new \Exception("Environment configuration does not support current host {$_SERVER['HTTP_HOST']}");
    }
    if (!file_exists($envDir . $envFile)) {
        throw new \Exception('Environment configuration does contain ' . $envFile);
    }
    Dotenv::load($envDir, $envFile);
    return getenv('APP_ENV') ?: 'production';
});