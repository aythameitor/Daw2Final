<?php

/** Database config array */

    return [
        'db' => [
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'name' => 'lovegaming',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]
        ]
    ]
?>