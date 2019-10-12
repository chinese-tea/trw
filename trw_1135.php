<?php

include './load.php';

$configArr = $ENV_USER['15277041135'];

$task = new TaskClass($app, $configArr);

$task->run();

