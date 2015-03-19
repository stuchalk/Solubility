<?php

class CitationsController extends AppController
{

	function index()
	{
		$this->Citation->virtualFields['first'] = 'UPPER(SUBSTR(Citation.journal,1,1))';
		$this->Citation->virtualFields['cite'] = 'CONCAT(Citation.journal," ",Citation.year,", ",Citation.volume,", ",Citation.firstpage)';
		$data=$this->Citation->find('list', array('fields'=>array('id','cite','first'),'order'=>array('first','cite')));
		$this->set('data',$data);
	}

	function view($id,$format="")
	{
		$data=$this->Citation->find('first', array('conditions'=>array('Citation.id'=>$id)));
		if($format=="json") { echo json_encode($data);exit; }
		$this->set('data',$data);
	}

	function authors($id,$format="")
	{
		$data=$this->Citation->find('first', array('conditions'=>array('Citation.id'=>$id)));
		if($format=="json") { echo json_encode($data['Author']);exit; }
		$this->set('data',$data['Author']);
		if($this->request->is('requested')) { return $data['Author']; }
	}
}
?>