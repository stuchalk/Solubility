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
        $type="";
        // Use id to find an InChI to search on
        $inchi=get_headers("http://cactus.nci.nih.gov/chemical/structure/".$id."/stdinchi",true);
        //echo "<pre>";print_r($inchi);echo "</pre>";exit;
        if(stristr($inchi[0],"OK")) {
            $data=$this->Chemical->find('first', ['conditions'=>['Chemical.inchi'=>$inchi],'recursive'=>2]);
        } else {
            if(preg_match('/([0-9]{2,7})-([0-9]{2})-[0-9]/',$id)) {
                $type="casrn";
                $data=$this->Chemical->find('all', ['conditions'=>['Chemical.casrn'=>$id],'order'=>['name','formula'],'recursive'=>1]);
            } elseif(preg_match('/[A-Z][a-z]?\d*|\((?:[^()]*(?:\(.*\))?[^()]*)+\)\d+/',$id)) {
                $type="formula";
                $data=$this->Chemical->find('all', ['conditions'=>['Chemical.formula'=>$id],'order'=>['name','formula'],'recursive'=>1]);
            } elseif(is_numeric($id)) {
                $type="id";
                $data=$this->Chemical->find('first', ['conditions'=>['Chemical.id'=>$id],'recursive'=>2]);
            } elseif(is_string($id)) {
                $type="name";
                $data=$this->Chemical->find('first', ['conditions'=>['Chemical.name'=>$id],'recursive'=>2]);
            } else {
                $data="No chemical found using '".$id."'";
            }
        }
        // Check name
        if(isset($data['Chemical']['inchi'])&&$data['Chemical']['inchi']=="") {
            $strpath="http://cactus.nci.nih.gov/chemical/structure/".rawurlencode($data['Chemical']['name'])."/stdinchi";
            $keypath="http://cactus.nci.nih.gov/chemical/structure/".rawurlencode($data['Chemical']['name'])."/stdinchikey";
            $test=get_headers($strpath,true);
            if(stristr($test[0],"OK")) {
                $data['Chemical']['inchi']=file_get_contents($strpath);
                $data['Chemical']['inchikey']=file_get_contents($keypath);
                $this->Chemical->save($data);
            }
        }
        //echo "<pre>";print_r($data);echo '</pre>';exit;
        if(!isset($data['Chemical'])) {
            $data=$this->Chemical->find('list', ['fields'=>['id','name','first'],'conditions'=>[$type=>$id],'order'=>['first','name']]);
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
		// Make data
		$input=$data['Chemical'];
		$input['url']=$path."chemicals/view/".$input['id'];unset($input['id']);
		$input['systems']=[];
		foreach($data['System'] as $s)
		{
			$input['systems'][]=['sysID'=>$s['sysID'],'title'=>$s['title'],'url'=>$path.'systems/view/'.$s['sysID']];
		}
		if($format=="xml")
		{
			$output="<?xml version='1.0'?>\n";
			$output.="<chemical>\n";
			foreach($input as $k1=>$v1)
			{
				$output.="<".$k1.">";
				if(is_array($v1))
				{
					foreach($v1 as $k2=>$v2)
					{
						if(is_numeric($k2)) { $k2="instance"; }
						$output.="<".$k2.">";
						if(is_array($v2))
						{
							foreach($v2 as $k3=>$v3)
							{
								$output.="<".$k3.">".$v3."</".$k3.">";
							}
						}
						$output.="</".$k2.">\n";
					}
				}
				else
				{
					$output.=$v1;
				}
				$output.="</".$k1.">\n";
			}
			$output.="</chemical>";
            $output=str_replace("&","&amp;",$output);
			// Output
			header('Content-type: text/xml');
			echo $output;exit;
		}
		elseif($format=="json")
		{
			$output=json_encode($input);
			// Output
			header('Content-type: application/json');
			echo $output;exit;
		}
		elseif($format=="jsonld")
		{
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
				,
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
			]];
			$input=['@context'=>$context]+$input;
			$output=json_encode($input);
			// Output
			header('Content-type: application/ld+json');
			echo $output;exit;
		}
		else
		{
			return;
		}
	}
}