<?php

/**
 * Class ChemicalsController
 * Methods to access citations
 */
class CitationsController extends AppController
{

    /**
     * Return all citations in the database
     * @param string $format
     */
	function index($format="")
	{
		$data=$this->Citation->find('list', ['fields'=>['id','cite','first'],'order'=>['first','cite']]);
        if($format=="json") { echo json_encode($data);exit; }
        $this->set('data',$data);
	}

    /**
     * View a specific citation
     * @param $id
     * @param string $format
     */
    function view($id,$format="")
	{
		$data=$this->Citation->find('first', ['conditions'=>['Citation.id'=>$id],'recursive'=>2]);
		if($format=="json") { echo json_encode($data);exit; }
		$this->set('data',$data);
	}

}