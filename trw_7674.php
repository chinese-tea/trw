<?php

include './load.php';

$configArr = array(
	'user_name' => '13367717674',
	'user_id' => 25471,
	'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0aW1lIjoxNTcwNzExNzE0LCJ1c2VyIjoiMzM5OTUifQ.JyRvdShExnb3H06XvznQSPrSgccl9vY4p1SHezrIDuc',
	'tb_tasks_status' => true,
	'tb_order_status' => false,
	'jd_order_status' => false,
);

$task = new TaskClass($app, $configArr);

$task->run();