<?php
function getHostInfo($extra = null) {
    $machine = parse_ini_file("rabbitMQ.ini", true);
    if ($extra != null) {
        foreach ($extra as $ini) {
            $parsed = parse_ini_file($ini, true);
            if ($parsed) {
                $machine = array_merge($machine, $parsed);
            }
        }
    }
    return $machine;
}
?>
