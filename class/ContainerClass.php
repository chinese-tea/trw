<?php


class ContainerClass{
	
	public $ch;
	public $env;
	
	function __construct($env){
		$this->init($env);
	}
	
	function init($env){
		$this->ch = curl_init();
		$this->env = $env;
	}
	
	
}
