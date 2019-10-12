<?php
set_time_limit(0); 
date_default_timezone_set('PRC');

$ch =curl_init();

while(1){
	
	echo date('Y-m-d H:i:s')."\n";
	
	
	$res = getList('tasks', 'tb'); //淘宝浏览单
	$taskList = array_slice($res->list, 0, 3);	
	$task = selectOrder($taskList);	
	if($task != null){ //如果接到了任务，打印任务信息，不再继续循环
		$r = grabTask($task->id, $task->not_match);
		if($r->code == '000'){
			prompt('淘宝浏览单',$task);
			break;
		}		
	}
	
	sleep(mt_rand(1,2));
	
	if(1){
		$res = getList('order', 'tb'); //淘宝购买单
		$taskList = array_slice($res->list, 0, 3);	
		$task = selectOrder($taskList, 8.0); 	
		if($task != null){ //如果接到了任务，打印任务信息，不再继续循环
			$r = grabTask($task->id, $task->not_match);
			if($r->code == '000'){
				prompt('淘宝购买单',$task);
				break;
			}		
		}
		
		sleep(mt_rand(1,2));
	}
	
	$res = getList('order', 'jd'); //京东购买单
	$taskList = array_slice($res->list, 0, 3);	
	$task = selectOrder($taskList);
	if($task != null){ //如果接到了任务，打印任务信息，不再继续循环
		$r = grabTask($task->id, $task->not_match);
		if($r->code == '000'){
			prompt('京东购买单',$task);
			break;
		}		
	}
	
	
	$t = mt_rand(1,3)/100; //1~2分钟随机抢单	
	sleep($t*60);//定时
}

/**
 * type: tasks浏览单,order购买单
 * shop_type: tb淘宝,jd京东
 **/
function getList($type, $shop_type){
	$url = 'http://api-wx.firstblog.cn/case/lists1?type=tasks&page=1&shop_type=tb&consumer_id=24718';
	
	$fields = array(
		'type' => $type,
		'page' => '1',
		'shop_type' => $shop_type,
		'consumer_id' => 24718
	);
	return decode(post($url, $fields));
}

//选出订单
function selectOrder($taskList, $momeylimit=0){
	$shop_name = array('精品刺绣馆','远航汽车导航直销店','全国企业彩铃定制中心','涵生珠宝','倍乐熊旗舰店','一诺能量水晶','情简时尚女装');
	$task = null;
	foreach($taskList as $v){
		//not_match意思是单子还没被抢完，然后在选出金额最大的那单
		if($v->not_match == 0 && (!in_array($v->name, $shop_name)) && ((float)$v->money >= $momeylimit) && ($task == null || (float)$v->money > (float)$task->money)){
			$task = $v;  
		}
	}
	return $task;
}


function grabTask($id, $not_match){
	$url = 'http://api-wx.firstblog.cn/case/getcase';
	$fields = array(
		'type' => 'tasks',
		'id' => $id,
		'not_match' => $not_match,
		'consumer_id' => 24718
	);
	
	return decode(post($url, $fields));
}

function prompt($name,$task){
	echo '接到任务'.$name.' 佣金：'.$task->money.'   任务id：'.$task->id.'  店铺名：'.$task->name;
	exec('start D:\jietu.png');
}

function post($url, $fields){
	global $ch;
	
	$cookie=dirname(__FILE__)."/cookie.txt";
	curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookie);
	
	curl_setopt($ch,CURLOPT_URL, $url);

	$header = array('token:eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ0aW1lIjoxNTcwNDk2NzIxLCJ1c2VyIjoiMzMyNTgifQ.iBdxPCYaDsjb18611qc3wLHfj_Eec7XvC7qJ6gFNvMc');

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch,CURLOPT_POST,true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

	curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
	//curl_setopt($ch,CURLOPT_COOKIE,'__cfduid=db20d88022a01ad7e748371a590b21e141537496085; PHPSESSID=334u28d7so6scn2v20hufg0pn0; seccode=1wDd5Bsh9zGO5yWi6v2frWweze5hEd9zE6R6ger; mId=1582; mUser=zdad456');
	
	return curl_exec($ch);
}
 
function decode($content){
	return json_decode($content);
}