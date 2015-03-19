<?php

class SystemtypesController extends AppController
{

	function index()
	{
		$this->Systemtype->virtualFields['first'] = 'UPPER(SUBSTR(Systemtype.title,1,1))';
		$data=$this->Systemtype->find('list', array('fields'=>array('id','title','first'),'order'=>array('first','title')));
		$this->set('data',$data);
	}

	function view($id)
	{
		$data=$this->Systemtype->find('first', array('conditions'=>array('Systemtype.id'=>$id),'recursive'=>2));
		$this->set('data',$data);
		if($this->request->is('requested')) { return $data; }
	}

}
?>