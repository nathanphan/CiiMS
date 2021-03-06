<?php

// Set Error Reporting Levels
error_reporting(-1);
ini_set('display_errors', 'true');
date_default_timezone_set ('UTC');

// Definitions
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

if (!isset($_SERVER['CIIMS_ENV']))
    $_SERVER['CIIMS_ENV'] = 'main';

$config = require __DIR__.DS.'protected'.DS.'config'.DS.$_SERVER['CIIMS_ENV'].'.php';
$defaultConfig = require __DIR__.DS.'protected'.DS.'config'.DS.'main.default.php';

// Load Yii and Composer extensions
require_once __DIR__.DS.'vendor'.DS.'yiisoft'.DS.'yii'.DS.'framework'.DS.'yii.php';
require_once __DIR__.DS.'vendor'.DS.'autoload.php';
Yii::setPathOfAlias('vendor', __DIR__.DS.'vendor');

$config = CMap::mergeArray($defaultConfig, $config);

$_SERVER['SERVER_NAME'] = 'localhost';

// Set the request component in the test script
$config['components']['request'] = array(
    'class' => 'vendor.codeception.YiiBridge.web.CodeceptionHttpRequest'
);

/**
 * @todo: Why does codeception require this? This needs to be pulled directly from protected/runtime/modules.config.php
 * If we let PHP generate this, it makes "array(0 => 'api', 1 => 'dashboard', 2 => 'install', 3 => 'hybridauth');",
 * Which _codeception_ refuses to parse when it passes it off to Yii
 */
$config['modules'] = array(
	'api',
	'dashboard',
	'install',
	'hybridauth'
);

// Return for Codeception
return array(
    'class' => 'CWebApplication',
    'config' => $config,
);
