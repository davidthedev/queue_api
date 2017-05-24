<?php

define('BASE_LOCATION', __DIR__);

require_once 'app/app.php';
require_once 'app/db/db.php';

$db = new DB;
$app = new App($db);

