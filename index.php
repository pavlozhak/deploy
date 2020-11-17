<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'src/Deploy.php';
require_once 'src/Config/Config.php';
require_once 'src/Exception/DeployException.php';
require_once 'src/Handlers/Git.php';
require_once 'src/Notification/TelegramNotification.php';
require_once 'vendor/autoload.php';

use Deploy\Deploy;

Deploy::run();
