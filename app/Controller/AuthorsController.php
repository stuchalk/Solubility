<?php

/**
 * Class AuthorsController
 * Methods to access authors
 */
class AuthorsController extends AppController
{

    /**
     * Return all authors in the database
     * @param string $format
     */
    function index($format="")
	{
		$this->Author->virtualFields['first'] = 'UPPER(SUBSTR(Author.lastname,1,1))';
		$this->Author->virtualFields['name'] = 'CONCAT(Author.firstname," ",Author.lastname)';
		$data=$this->Author->find('list', ['fields'=>['id','name','first'],'order'=>['first','name']]);
        if($format=="json") { echo json_encode($data);exit; }
        $this->set('data',$data);
	}

    /**
     * View a specific author
     * @param $id
     * @param string $format
     */
    function view($id,$format="")
	{
		$this->Author->virtualFields['name'] = 'CONCAT(Author.firstname," ",Author.lastname)';
		$data=$this->Author->find('first', ['conditions'=>['Author.id'=>$id]]);
		if($format=="json") { echo json_encode($data);exit; }
		$this->set('data',$data);
	}

}