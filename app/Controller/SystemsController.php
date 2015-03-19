<?php

class SystemsController extends AppController
{
	public $name = 'Systems';
	
	function view($sysID)
	{
		$this->set('data',$this->System->find('first', array('conditions'=>array('System.sysID'=>$sysID),'recursive'=>1)));
	}

}
?>