<?php

$objSubframe = new \Webcitron\Subframe\Core\Subframe();
$objApp = $objSubframe->getApp();
$objResponse = $objApp->launch();
echo $objResponse;
