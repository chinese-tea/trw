<?php

include './load.php';

$configArr = array(
	'user_name' => '15240651556',
	'user_id' => 24718,
	'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0aW1lIjoxNTcwMzY0MjMxLCJ1c2VyIjoiMzMyNTgifQ.mh8bbr0CYKoWQeoY8TW1jO6Lie-D5XYRxXNI3RfJ-5E',
	'tb_tasks_status' => true,
	'tb_order_status' => true,
	'jd_order_status' => true,
);

$task = new TaskClass($app, $configArr);

$task->run();