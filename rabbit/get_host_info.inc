<?php
function getHostInfo($extra = null) {
    $machine = parse_ini_file("rabbitMQ.ini", true);

    // Check if $machine is an array
    if (!is_array($machine)) {
        throw new Exception("Failed to parse rabbitMQ.ini or file not found.");
    }
    if ($extra != null) {
        foreach ($extra as $ini) {
            $parsed = parse_ini_file($ini, true);
            // Check if $parsed is an array
            if (is_array($parsed)) {
                $machine = array_merge($machine, $parsed);
            } else {
                throw new Exception("Failed to parse $ini or file not found.");
            }
        }
    }

    return $machine;
}
?>
