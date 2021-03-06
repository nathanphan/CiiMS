<?php
/**
 * This is the base installer for CiiMS
 * By default, this bootstraps protected/modules/install/installer.php for the initial installation 
 * and configuration for Yii. After that, it passes controller to Yii::createWebApplication so that
 * We can run a better install (migrations, config writing, etc...)
 * 
 * If you want to make any changes to the PRE YiiPath install, edit /protected/modules/install/installer.php
 * Otherwise, this is a basic Yii App running only the install module 
 */

error_reporting(-1);
ini_set('display_errors', 'true');

$yiiPath = __DIR__.DS.'vendor'.DS.'yiisoft'.DS.'yii'.DS.'framework'.DS.'yiilite.php';
require_once __DIR__.DS.'vendor'.DS.'autoload.php';

$config=dirname(__FILE__).'/protected/config/install.php';
$mainConfig = dirname(__FILE__).'/protected/config/main.php';
$ciimsConfig = require($config);

defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);
defined('CIIMS_INSTALL') or define('CIIMS_INSTALL', true);
if (!file_exists($mainConfig) && !file_exists($yiiPath)) 
{
    require(dirname(__FILE__).'/protected/modules/install/init.php');
    exit();
}

require_once($yiiPath);

Yii::setPathOfAlias('vendor', __DIR__.DS.'vendor');
$app = Yii::createWebApplication($config);
$app->run();
