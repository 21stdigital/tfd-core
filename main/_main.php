<?php


$directories = glob(dirname(__FILE__).'/*', GLOB_ONLYDIR);

foreach ($directories as $index => $path) {
    require_once $path.'/_main.php';
}
