<?php


class TaskClass{
	
	protected $app;
	protected $ch;
	protected $configArr;
	
	function __construct($app, $configArr){
		$this->app = $app;
		$this->ch = $app->ch;
		$this->configArr = $configArr;
		
	}
	
	function run(){
		while(1){
	
			echo date('Y-m-d H:i:s')."\n";
			
			if($this->configArr['tb_tasks_status']){
				$res = $this->getList('tasks', 'tb'); //淘宝浏览单
				$taskList = array_slice($res->list, 0, 3);	
				$task = $this->selectOrder($taskList);	
				if($task != null){ //如果接到了任务，打印任务信息，不再继续循环
					$r = $this->grabTask($task->id, $task->not_match, 'tasks', 'tb');
					if($r->code == '000'){
						$this->prompt('淘宝浏览单',$task);
						break;
					}		
				}
			}
			
			if($this->configArr['tb_order_status']){
				sleep(mt_rand(0,2));
				
				$res = $this->getList('order', 'tb'); //淘宝购买单
				$taskList = array_slice($res->list, 0, 3);	
				$task = $this->selectOrder($taskList, $this->configArr['minmoney']);	
				if($task != null){ //如果接到了任务，打印任务信息，不再继续循环
					$r = $this->grabTask($task->id, $task->not_match, 'order', 'tb');
					if($r->code == '000'){
						$this->prompt('淘宝购买单',$task);
						break;
					}		
				}
			}
			
			if($this->configArr['jd_order_status']){
				sleep(mt_rand(0,2));
				
				$res = $this->getList('order', 'jd'); //京东购买单
				$taskList = array_slice($res->list, 0, 3);	
				$task = $this->selectOrder($taskList);	
				if($task != null){ //如果接到了任务，打印任务信息，不再继续循环
					$r = $this->grabTask($task->id, $task->not_match, 'order', 'jd');
					if($r->code == '000'){
						$this->prompt('京东购买单',$task);
						break;
					}		
				}
			}
			
			if($this->configArr['tb_order_status'] || $this->configArr['jd_order_status']){
				sleep(mt_rand(0,2));
			} else {
				$t = mt_rand(3,6)/100; //1~2分钟随机抢单
				sleep($t*60);//定时
			}
				
			
		}
	}
	
	function getUserId($shop_type){
		if($shop_type == 'jd'){
			$user_id = $this->configArr['jd_user_id'];
		} else {
			$user_id = $this->configArr['user_id'];
		}
		return $user_id;
	}
	
	
	/**
	 * type: tasks浏览单,order购买单
	 * shop_type: tb淘宝,jd京东
	 **/
	function getList($type, $shop_type){
		
		$user_id = $this->getUserId($shop_type);
		
		$url = 'http://api-wx.firstblog.cn/case/lists1?type='.$type.'&page=1&shop_type=tb&consumer_id='.$user_id;
		
		$fields = array(
			'type' => $type,
			'page' => '1',
			'shop_type' => $shop_type,
			'consumer_id' => $user_id
		);
		return $this->decode($this->post($url, $fields));
	}

	//选出订单
	function selectOrder($taskList, $momeylimit=0){
		//'精品刺绣馆','远航汽车导航直销店','全国企业彩铃定制中心','涵生珠宝','倍乐熊旗舰店','一诺能量水晶','情简时尚女装','美之缘家居护理体验馆','上汽零部件自营店','evafang时尚尖货','艺博陶瓷家居馆','赫泰旗舰店'
		$shop_name = array('佳地素人女装高端定制','合创睦家美妆拼购专营店');
		$task = null;
		foreach($taskList as $v){
			//not_match意思是单子还没被抢完，然后在选出金额最大的那单
			if(  $v->not_match == 0 && (!in_array($v->name, $shop_name)) && ($this->taskMoney($v) >= $momeylimit) && ($task == null || $this->taskMoney($v) > $this->taskMoney($task) ) ){
				$task = $v;  
			}
		}
		return $task;
	}


	function grabTask($id, $not_match, $type, $shop_type){
		$user_id = $this->getUserId($shop_type);
		
		$url = 'http://api-wx.firstblog.cn/case/getcase';
		$fields = array(
			'type' => $type,
			'id' => $id,
			'not_match' => $not_match,
			'consumer_id' => $user_id
		);
		
		return $this->decode($this->post($url, $fields));
	}
	
	function taskMoney($task){
		if(isset($task->fee)){
			return (float)$task->money + (float)$task->fee;
		}
		return (float)$task->money;
	}

	function prompt($name,$task){
		echo $this->configArr['user_name'].'接到任务'.$name."\n\r 佣金：".$task->money.'   任务id：'.$task->id.'  店铺名：'.$task->name;
		exec($this->app->env['prompt']['type'][$this->app->env['prompt']['type_id']]);
	}

	function post($url, $fields){
		
		$cookie=dirname(__FILE__)."/cookie.txt";
		curl_setopt ($this->ch, CURLOPT_COOKIEJAR, $cookie);
		curl_setopt ($this->ch, CURLOPT_COOKIEFILE, $cookie);
		
		curl_setopt($this->ch,CURLOPT_URL, $url);

		$header = array('token:'.$this->configArr['token']);

		curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($this->ch,CURLOPT_POST,true);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,true);

		curl_setopt($this->ch,CURLOPT_HTTPHEADER,$header);
		//curl_setopt($this->ch,CURLOPT_COOKIE,'__cfduid=db20d88022a01ad7e748371a590b21e141537496085; PHPSESSID=334u28d7so6scn2v20hufg0pn0; seccode=1wDd5Bsh9zGO5yWi6v2frWweze5hEd9zE6R6ger; mId=1582; mUser=zdad456');
		
		return curl_exec($this->ch);
	}
	 
	function decode($content){
		return json_decode($content);
	}
	
	
}
