<?php
$objSubframe = new Subframe();
$objApp = $objSubframe->getApp();
$objResponse = $objApp->launch();
echo $objResponse;
