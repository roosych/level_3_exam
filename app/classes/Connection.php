<?php

namespace App\classes;

use PDO;

class Connection
{
    public static function make() {
        return new PDO('mysql:host=127.0.0.1;dbname=level3;charset=utf8', 'root', '');
    }
}