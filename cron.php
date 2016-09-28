<?php
/**
 * Run cron tasks.
 */

include_once 'config/glancrConfig.php';
require_once 'vendor/autoload.php';

use glancr\SystemUpdater;
use glancr\ModuleUpdater;

$systemInfo = getSystemInfo();

try {
    $systemUpdate = new SystemUpdater($systemInfo);
    $systemUpdate->checkSystemUpdate();
} catch (Exception $e) {
    error_log($e->getMessage());
}

try {
    $moduleUpdates = new ModuleUpdater();
    $moduleUpdates->checkModuleUpdates();
} catch (Exception $e) {
    error_log($e->getMessage());
}

if(empty($e)) {
    error_log("cron ran successfully");
} else {
    error_log("cron run failed with errors, see above.");
}