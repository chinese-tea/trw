<?php

include './load.php';

$configArr = $ENV_USER['13367717674'];

$task = new TaskClass($app, $configArr);

$task->run();

