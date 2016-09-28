<?php

namespace glancr;
use \VisualAppeal\AutoUpdate;
use \Monolog;
use \Desarrolla2;

class SystemUpdater
{
    private $currentVersion;
    private $update;

    function __construct($versionInfo) {
        $this->currentVersion = $versionInfo->version;

        //         Download the zip update files to `__DIR__ . '/temp'`
        // Copy the contents of the zip file to the current directory `__DIR__`
        // The update process should last 60 seconds max
        $this->update = new AutoUpdate(GLANCR_ROOT . '/tmp', GLANCR_ROOT, 60);
        $this->update->setCurrentVersion($this->currentVersion);
        $this->update->setUpdateUrl(GLANCR_API_BASE . '/update/system');
        // The following two lines are optional
        $this->update->addLogHandler(new Monolog\Handler\StreamHandler(GLANCR_ROOT . '/update.log'));

        //@FIXME: Caching updates seems broken on latest revision, don't use it in the meantime
//        $this->update->setCache(new Desarrolla2\Cache\Adapter\File(GLANCR_ROOT . '/cache'), 3600);
    }

    /**
     * Checks if there is an update available for the Glancr base system.
     * @param $versionInfo object
     * Object with keys version and update_url, @see getSystemInfo() in glancrConfig.php
     */
    function checkSystemUpdate() {
        // Check for a new update
        if ($this->update->checkUpdate() === false)
            die('Could not check for updates! See log file for details.');

        // Check if new update is available
        if ($this->update->newVersionAvailable()) {
            //Install new update
            $newVersion = $this->update->getLatestVersion()->getVersion();

            if (getConfigValue('system_updates') === $newVersion) {
                return;
            } else {
                setConfigValue('system_updates', $newVersion);
                $api = new GlancrServerApi(GLANCR_API_BASE);
                $result = $api->triggerMail('update');
                error_log($result);
            }
        } else {
            // No new update
            return;
        }
    }

    /**
     * Update mirr.OS to the latest version.
     * @return string Result of the update operation. Errors are retrieved from \VisualAppeal\AutoUpdate update() method.
     */
    function updateSystem() {
        $result = $this->update->update();
        if ($result === true) {
            return _("mirr.OS sucessfully updated");
        } else {
            return _("There was an error updating mirr.OS: ") . $result;
        }
    }
}
