<?php
namespace guayaquil;

use Dotenv\Dotenv;

define('GUAYAQUIL_DIR', __DIR__);

(new Dotenv(__DIR__))->load();

router::start();
