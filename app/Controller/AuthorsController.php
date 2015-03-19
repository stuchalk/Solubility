<?php

class AuthorsController extends AppController
{

	function index()
	{
		$this->Author->virtualFields['first'] = 'UPPER(SUBSTR(Author.lastname,1,1))';
		$this->Author->virtualFields['name'] = 'CONCAT(Author.firstname," ",Author.lastname)';
		$data=$this->Author->find('list', array('fields'=>array('id','name','first'),'order'=>array('first','name')));
		$this->set('data',$data);
	}

	function view($id,$format="")
	{
		$this->Author->virtualFields['name'] = 'CONCAT(Author.firstname," ",Author.lastname)';
		$data=$this->Author->find('first', array('conditions'=>array('Author.id'=>$id)));
		if($format=="json") { echo json_encode($data);exit; }
		$this->set('data',$data);
	}

}
?>