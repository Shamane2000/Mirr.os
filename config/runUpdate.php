<?php

require_once '../vendor/autoload.php';
include('glancrConfig.php');


switch ($_POST['type']) {
    case 'system':
        $update = new \glancr\SystemUpdater(getSystemInfo());
        $result = $update->updateSystem();
        return $result;
        break;
    case 'module':
        $update = new \glancr\ModuleUpdater();
        $result = $update->updateModule($_POST['name'], $_POST['version']);
        if($result === true) {
            http_response_code(200);
        } else {
            http_response_code(500);
        }
        break;
    default:
        return false;

}