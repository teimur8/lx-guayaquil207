<?php
namespace guayaquil;

use Dotenv\Dotenv;

define('GUAYAQUIL_DIR', __DIR__);

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

router::start();
