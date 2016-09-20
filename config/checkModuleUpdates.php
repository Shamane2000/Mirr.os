<?php
/**
 * Checks the glancr API if all installed modules are up-to-date.
 */

include_once('glancrServerApi.php');

include('glancrConfig.php');

$modules_content = scandir($basedir .'/modules');
$modules_available = [];
$updates_available = [];

foreach($modules_content as $file) {
    if(is_dir($basedir .'/modules/' . $file) && $file != '.' && $file != '..') {
        $modules_available[] = $file;
    }
}
$serverVersions = json_decode(file_get_contents($apibaseurl .'/update/getVersions.php'), true);
$onServer = array_keys($serverVersions);


foreach($modules_available as $module_available) {
    $versionFile = file_get_contents($basedir . '/modules/' . $module_available . '/version.txt');

    if (array_key_exists($module_available, $serverVersions)) {
        unset($onServer[array_search($module_available, $onServer)]);

        if ($versionFile < $serverVersions[$module_available]) {


//            echo "<td><a href='#' class='deleteModule' id='delete_$module_available'>delete</a></td><td><a class='updateModule' id='update_$module_available' href='#'>update to <span id='version_$module_available'>" . $serverVersions[$module_available] . "</span></a></td></tr>\n";
            $updates_available[] = [
                'name' => $module_available,
                'oldVersion' => $versionFile,
                'newVersion' => $serverVersions[$module_available]
            ];
        }
    }
}

if (!empty($updates_available)) {
    setConfigValue('module_updates', serialize($updates_available));
    $api = new \glancr\glancrServerApi($apibaseurl);
    $api->triggerMail('update');
}