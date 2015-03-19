<?php

class VolumesController extends AppController
{

	function index()
	{
		//$this->Citation->virtualFields['first'] = 'UPPER(SUBSTR(Citation.journal,1,1))';
		//$this->Citation->virtualFields['cite'] = 'CONCAT(Citation.journal," ",Citation.year,", ",Citation.volume,", ",Citation.firstpage)';
		$data=$this->Volume->find('all', array('fields'=>array('id','vol','title','url'),'order'=>array('vol'),'recursive'=>0));
		$this->set('data',$data);
	}

	function view($vol,$format="")
	{
		$data=$this->Volume->find('first', array('conditions'=>array('Volume.vol'=>$vol)));
		if($format=="json") { echo json_encode($data);exit; }
		$this->set('data',$data);
	}

}
?>