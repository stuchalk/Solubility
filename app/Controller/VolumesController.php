<?php
App::uses('Xml','Utility');

/**
 * Class VolumesController
 */
class VolumesController extends AppController
{
    /**
     * Show all volumes
     */
	public function index()
	{
		$data=$this->Volume->find('all',['fields'=>['id','vol','title','url'],'order'=>['vol'],'recursive'=>0]);
        $this->set('base',Configure::read('host.base'));
        $this->set('data',$data);
	}

    /**
     * View a volume (by volume #)
     * @param $vol
     * @param string $format
     */
    public function view($vol,$format="")
	{
		$data=$this->Volume->find('first',['conditions'=>['Volume.vol'=>$vol],'recursive'=>2]);

        if($format=="json") {
            header('Content-Type: application/json');
            echo json_encode($data);exit;
        }
        if($format=="xml") {
            $xmlArray = ['sds' => $data];
            $xmlObject = Xml::fromArray($xmlArray,['format'=>'tags']);
            header('Content-Type: application/xml');
            echo $xmlObject->asXML();exit;
        }
        $this->set('base',Configure::read('host.base'));
        $this->set('data',$data);
	}

    /** Scrape the volume data */
    public function scrape()
    {
        // Define the URL of the dataSeries page
        $volpath="http://srdata.nist.gov/solubility/dataSeries.aspx";
        // Import the content of the webpage into an array
        $volfile=file($volpath);
        // Look into each line of the file (array element) and keep lines that contain 'sol_sys.aspx'
        // The loop needs to count down as the unset function resets the array keys
        for($x=count($volfile)-1;$x>-1;$x--) {
            if(!stristr($volfile[$x],'sol_sys.aspx')) { unset($volfile[$x]); }
        }
        //echo '<pre>';print_r($volfile);echo "</pre>";exit;
        // Separate out the volume # and title using the explode() function and save to the database
        $data=[];
        foreach($volfile as $line) {
            list(,$idvoltitle)=explode("nm_dataSeries=",$line);
            list($nistid,$voltitle)=explode("\">Volume ",$idvoltitle);
            list($voltitle,)=explode("</a",$voltitle);
            list($vol,$title)=explode(". ",$voltitle);
            $data[]=['nistid'=>$nistid,'volume'=>$vol,'title'=>$title];
            // Save to database
            $this->Volume->create();
            $this->Volume->save(['Volume'=>['nistid'=>$nistid,'vol'=>$vol,'title'=>$title]]);
            $this->Volume->clear();
        }
        $this->set('base',Configure::read('host.base'));
        $this->set('data',$data);
    }
}