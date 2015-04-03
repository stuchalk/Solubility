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
		$data=$this->Systemtype->find('list',['fields'=>['id','title','first'],'order'=>['first','title']]);
        $this->set('base',Configure::read('host.base'));
        $this->set('data',$data);
	}

    /**
     * View a system type (by DB id)
     * @param $id
     * @return mixed
     */
	function view($id)
	{
		$data=$this->Systemtype->find('first',['conditions'=>['Systemtype.id'=>$id],'recursive'=>2]);
        $this->set('base',Configure::read('host.base'));
        $this->set('data',$data);
		if($this->request->is('requested')) { return $data; }
	}

    /** Get the volume data */
    public function scrape()
    {
        // Define the URL of the dataSeries page
        $systypepath="http://srdata.nist.gov/solubility/sys_category.aspx";

        // Import the content of the webpage into an array
        $systypefile=file($systypepath);

        // Look into each line of the file (array element) and keep lines that contain '<option' (from the select input field)
        // The loop needs to count down as the unset function resets the array keys
        for($x=count($systypefile)-1;$x>-1;$x--) {
            if(!stristr($systypefile[$x],'<option')) { unset($systypefile[$x]); }
        }

        // Separate out the volume # and title using the explode() function and save to the database
        $data=[];
        foreach($systypefile as $line) {
            list(,$systypetitle)=explode("value=\"",$line);
            list($systypetitle,)=explode("</option",$systypetitle);
            list($systype,$title)=explode("\">",$systypetitle);
            $data[$systype]=$title;
            // Save to database
            //$this->Systemtype->create();
            //$this->Systemtype->save(['Systemtype'=>['sysID'=>$systype,'title'=>$title]]);
            //$this->Systemtype->clear();
        }
        $this->set('base',Configure::read('host.base'));
        $this->set('data',$data);
    }
}