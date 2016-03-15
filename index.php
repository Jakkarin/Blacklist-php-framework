<?php

ini_set('error_reporting', E_ALL);
/**
 * ตรวจสอบ เวอร์ชั่นของ php
 */
if (version_compare(phpversion(), '5.4', '<'))
    die('php this version is out of date please upgrade before run this framework.');

/**
 * ประกาศที่ตั้งของ APP ตั้งแต่ Root directorty
 */
define('APP_ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_PATH', APP_ROOT . 'app' . DIRECTORY_SEPARATOR);
define('APP_CONTENT', APP_ROOT . 'contents' . DIRECTORY_SEPARATOR);
define('APP_SYSTEM', APP_ROOT . 'systems' . DIRECTORY_SEPARATOR);

/**
 * PSR-4: Autoloader
 * @var Autoloader
 */
spl_autoload_register(function($className) {
    require APP_ROOT . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';
});

/**
 * ตั้งค่า timezone และ load class App
 */
$app = systems\Config::get('app');
define('HTTP_HOST_TOKEN', md5($app['DefaultDomain']));
date_default_timezone_set($app['TimeZone']);

systems\App::run($app);
