<?php 
$info = fsockopen('192.168.66.3',1130,$errCode,$errStr, 1);
var_dump($info);
?>