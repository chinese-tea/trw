<?php


class ContainerClass{
	
	public $ch;
	
	function __construct(){
		$this->init();
	}
	
	function init(){
		$this->ch =curl_init();
	}
	
	
}
