<?php
namespace guayaquil;

use Dotenv\Dotenv;

define('GUAYAQUIL_DIR', __DIR__);

Dotenv::createImmutable(__DIR__)->load();

router::start();
