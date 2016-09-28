<?php

namespace glancr;

use ZipArchive;

class ModuleUpdater
{

    function __construct() {
    }

    /**
     * Checks the glancr API if all installed modules are up-to-date.
     */
    function checkModuleUpdates() {
        $modules_content = scandir(GLANCR_ROOT .'/modules');
        $modules_available = [];
        $updates_available = [];

        foreach($modules_content as $file) {
            if(is_dir(GLANCR_ROOT .'/modules/' . $file) && $file != '.' && $file != '..') {
                $modules_available[] = $file;
            }
        }
        $serverVersions = json_decode(file_get_contents(GLANCR_API_BASE .'/update/getVersions.php'), true);
        $onServer = array_keys($serverVersions);


        foreach($modules_available as $module_available) {
            $versionFile = file_get_contents(GLANCR_ROOT . '/modules/' . $module_available . '/version.txt');

            if (array_key_exists($module_available, $serverVersions)) {
                unset($onServer[array_search($module_available, $onServer)]);

                if ($versionFile < $serverVersions[$module_available]) {
                    $updates_available[] = [
                        'name' => $module_available,
                        'oldVersion' => $versionFile,
                        'newVersion' => $serverVersions[$module_available]
                    ];
                }
            }
        }

        if (!empty($updates_available)) {
            error_log("Module updates available: " . print_r($updates_available, true));

            if (unserialize(getConfigValue('module_updates')) === $updates_available) {
                return;
            } else {
                setConfigValue('module_updates', serialize($updates_available));
                $api = new glancrServerApi(GLANCR_API_BASE);
                $result = $api->triggerMail('update');
                error_log($result);
            }
        }
    }

    function updateModule($name, $version) {
        //@TODO: Refactor this with Guzzle/cURL or similar.
        file_get_contents(GLANCR_API_BASE . '/update/zipModule.php?module=' . $name);

        $file = GLANCR_API_BASE . '/update/moduleZips/' . $name . '-' . $version . '.zip';
        $tmpFile = GLANCR_ROOT . '/tmp/tmp_file.zip';

        if (!copy($file, $tmpFile)) {
            http_response_code(500);
            exit("failed to create $tmpFile from $file...\n");
        }

        $zip = new ZipArchive();
        if ($zip->open($tmpFile, ZIPARCHIVE::CREATE)) {
            $zip->extractTo(GLANCR_ROOT .'/modules/');
            $zip->close();
            http_response_code(200);
        } else {
            http_response_code(500);
        }

        unlink($tmpFile);
        setConfigValue('reload', '1');
    }
}
