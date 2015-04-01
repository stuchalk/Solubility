<?php

/**
 * Class SystemtypesController
 */
class SystemtypesController extends AppController
{

    /**
     * Show all system types
     */
	function index()
	{
		$this->Systemtype->virtualFields['first'] = 'UPPER(SUBSTR(Systemtype.title,1,1))';
		$data=$this->Systemtype->find('list', ['fields'=>['id','title','first'],'order'=>['first','title']]);
		$this->set('data',$data);
	}

    /**
     * View a system type (by DB id)
     * @param $id
     * @return mixed
     */
	function view($id)
	{
		$data=$this->Systemtype->find('first', ['conditions'=>['Systemtype.id'=>$id],'recursive'=>2]);
		$this->set('data',$data);
		if($this->request->is('requested')) { return $data; }
	}

}
?>