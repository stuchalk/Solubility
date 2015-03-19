<?php

class SystemsController extends AppController
{
	public $name = 'Systems';
	public $uses = ['System','Citation'];

	function view($sysID)
	{
        $data=$this->System->find('first', ['conditions'=>['System.sysID'=>$sysID],'recursive'=>1]);
        $citation=$this->Citation->find('first', ['conditions'=>['Citation.id'=>$data['Citation']['id']]]);
        $data['Author']=$citation['Author'];
        $this->set('data',$data);
	}

}
?>