<?php

require(__DIR__ . '/../vendor/autoload.php');
include_once ('glancrConfig.php');

use \VisualAppeal\AutoUpdate;

// Download the zip update files to `__DIR__ . '/temp'`
// Copy the contents of the zip file to the current directory `__DIR__`
// The update process should last 60 seconds max
$update = new AutoUpdate($basedir . '/tmp', $basedir, 60);
$update->setCurrentVersion('0.1.0'); // Current version of your application. This value should be from a database or another file which will be updated with the installation of a new version
$update->setUpdateUrl($apibaseurl . '/update/system'); //Replace the url with your server update url

// The following two lines are optional
$update->addLogHandler(new Monolog\Handler\StreamHandler($basedir . '/update.log'));
$update->setCache(new Desarrolla2\Cache\Adapter\File($basedir . '/cache'), 3600);

//Check for a new update
if ($update->checkUpdate() === false)
    die('Could not check for updates! See log file for details.');

// Check if new update is available
if ($update->newVersionAvailable()) {
    //Install new update
    echo 'New Version: ' . $update->getLatestVersion();
    $update->update();
} else {
    // No new update
    echo 'Your application is up to date';
}