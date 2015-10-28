<?php

/**
 * Class ChemicalsController
 * Methods to access chemicals
 */
class ChemicalsController extends AppController
{

    /**
     * Return all chemicals in the database
     */
	public function index()
	{
		$data=$this->Chemical->find('list', ['fields'=>['id','name','first'],'order'=>['first','name']]);
		$this->set('data',$data);
        $this->set('nist',Configure::read('url.base'));
	}

    /**
     * View a specific chemical
     * @param $id
     * @param string $format
     */
    public function view($id,$format="")
	{
        $type="";$data=[];

        // Use id to find an InChI to search on
        if(stristr($id,'InChI')) {
            $chk=['OK'];
            $inchi=$id;
        } else {
            // Can we get a valid inchi from the CIR site
            $url="http://cactus.nci.nih.gov/chemical/structure/".$id."/stdinchi";
            $chk=get_headers($url,true);
            if(stristr($chk[0],"OK")) {
                $inchi = file_get_contents($url);
            }
        }

        // Find the id/inchi in the database
        if(stristr($chk[0],"OK")) {
            $type="viainchi";
            $data=$this->Chemical->find('first', ['conditions'=>['Chemical.inchi'=>$inchi],'recursive'=>2]);
        }
        if(empty($data)) {
            if(preg_match('/([A-Z]{14})-([AZ]{9})-[AZ]/',$id)) {
                $type="inchikey";
                $data=$this->Chemical->find('all', ['conditions'=>['Chemical.casrn'=>$id],'order'=>['name','formula'],'recursive'=>1]);
            }
        }
        if(empty($data)) {
            if(preg_match('/([0-9]{2,7})-([0-9]{2})-[0-9]/',$id)) {
                $type="casrn";
                $data=$this->Chemical->find('all', ['conditions'=>['Chemical.casrn'=>$id],'order'=>['name','formula'],'recursive'=>1]);
            }
        }
        if(empty($data)) {
            if (preg_match('/[A-Z][a-z]?\d*|\((?:[^()]*(?:\(.*\))?[^()]*)+\)\d+/', $id)) {
                $type = "formula";
                $data = $this->Chemical->find('all', ['conditions' => ['Chemical.formula' => $id], 'order' => ['name', 'formula'], 'recursive' => 1]);
            }
        }
        if(empty($data)) {
            if(is_numeric($id)) {
                $type="id";
                $data=$this->Chemical->find('first', ['conditions'=>['Chemical.id'=>$id],'recursive'=>2]);
            }
        }
        if(empty($data)) {
            $type = "name";
            $data = $this->Chemical->find('all', ['conditions' => ['Chemical.name' => $id], 'recursive' => 2]);
        }
        if(empty($data)) {
            $type = "inname";
            $data=$this->Chemical->find('all', ['conditions'=>['Chemical.name like'=>'%'.$id.'%'],'recursive'=>2]);
        }

        // Check name
        if(!empty($data)&&isset($data['Chemical']['inchi'])&&$data['Chemical']['inchi']=="") {
            $strpath="http://cactus.nci.nih.gov/chemical/structure/".rawurlencode($data['Chemical']['name'])."/stdinchi";
            $keypath="http://cactus.nci.nih.gov/chemical/structure/".rawurlencode($data['Chemical']['name'])."/stdinchikey";
            $test=get_headers($strpath,true);
            if(stristr($test[0],"OK")) {
                $data['Chemical']['inchi']=file_get_contents($strpath);
                $data['Chemical']['inchikey']=str_replace("InChIKey=","",file_get_contents($keypath));
                $this->Chemical->save($data);
            }
        }

        // Work out how to present data in view based on # hits on chemical 'id'
        // This is needed as name, formula, casrn, and inchikey can potentially return multiple hits
        if(!isset($data['Chemical'])&&count($data)>1) {
            if($type=="inname") {
                $data=$this->Chemical->find('list', ['fields'=>['id','name','first'],'conditions'=>['name like'=>'%'.$id.'%'],'order'=>['first','name']]);
            } else {
                $data=$this->Chemical->find('list', ['fields'=>['id','name','first'],'conditions'=>[$type=>$id],'order'=>['first','name']]);
            }
            $this->set('data',$data);
            $this->set('nist',Configure::read('url.base'));
            $this->render('index');
        } else {
            if(isset($data[0])) { $data=$data[0]; }
            $this->set('data',$data);
            $this->set('type',$type);
            $this->set('base',Configure::read('host.base'));
            $this->set('nist',Configure::read('url.base'));
            if($format!="") { $this->export($data,$format); }
        }
    }

     /**
     * Generic export public function for actions above
     * @param $data
     * @param string $format
     */
	public function export($data,$format="xml")
	{
		$path=Configure::read('host.base');
		// Make output data
		$output=$data['Chemical'];
        $output['url']=$path."chemicals/view/".$output['id'];unset($output['id']);
        // Add system data
        $output['systems']=[];
		foreach($data['System'] as $s) {
            $output['systems'][]=['sysID'=>$s['sysID'],'title'=>$s['title'],'url'=>$path.'systems/view/'.$s['sysID']];
		}
        // Output data
		if($format=="xml") {
            $this->Export->xml($output['name'],"chemical",$output);
		} elseif($format=="json") {
            $this->Export->json($output['name'],"chemical",$output);
		} elseif($format=="jsonld") {
			$context=[
				"name"=>[
					"@id"=>"http://semanticscience.org/resource/CHEMINF_000043",
					"@type"=>"http://www.w3c.org/2001/XMLSchema#string"
				],
				"formula"=>[
					"@id"=>"http://semanticscience.org/resource/CHEMINF_000037",
					"@type"=>"http://www.w3c.org/2001/XMLSchema#string"
				],
				"casrn"=>[
					"@id"=>"http://edamontology.org/data_1002",
					"@type"=>"http://www.w3c.org/2001/XMLSchema#string"
				],
				"inchikey"=>[
					"@id"=>"http://purl.obolibrary.org/obo/ERO_0001044",
					"@type"=>"http://www.w3c.org/2001/XMLSchema#string"
				],
				"updated"=>[
					"@id"=>"http://purl.org/dc/terms/modified",
					"@type"=>"http://www.w3c.org/2001/XMLSchema#dateTime"
				],
				"url"=>[
					"@id"=>"http://schema.org/url",
					"@type"=>"@id"
				],
				"systems"=>[
					"sysID"=>[
						"@id"=>"http://purl.org/dc/terms/identifier",
						"@type"=>"http://www.w3c.org/2001/XMLSchema#string"
					],
					"title"=>[
						"@id"=>"http://purl.org/dc/terms/title",
						"@type"=>"http://www.w3c.org/2001/XMLSchema#string"
					],
					"url"=>[
						"@id"=>"http://schema.org/url",
						"@type"=>"@id"
					]
				]
			];
            $this->Export->jsonld($output['name'],"chemical",$output,$context);
		} else {
			return;
		}
	}
}