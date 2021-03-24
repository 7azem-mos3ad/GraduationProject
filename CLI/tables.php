<?php
return $tables = [
    'Users' => "
        username VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        talk BOOLEAN NOT NULL,
    ",
];
