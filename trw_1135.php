<?php

include './load.php';

$configArr = array(
	'user_name' => '15277041135',
	'user_id' => 25508,
	'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0aW1lIjoxNTcwNjI1MDUzLCJ1c2VyIjoiMzM4ODMifQ.IdIQOGBnXSX1lwR_oTlji2NHp-Mf3jt2uUEgwBQB4Io',
	'tb_tasks_status' => true,
	'tb_order_status' => false,
	'jd_order_status' => false,
);

$task = new TaskClass($app, $configArr);

$task->run();