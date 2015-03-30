<?php

/**
 * Class SystemsController
 * Methods to access systems
 */
class SystemsController extends AppController
{
	public $name = 'Systems';
	public $uses = ['System','Citation'];

    /**
     * Return all systems in the database
     */
    public function index()
    {
        $this->System->virtualFields['first'] = 'UPPER(SUBSTR(System.title,1,1))';
        $data=$this->System->find('list', ['fields'=>['sysID','title','first'],'order'=>['first','title']]);
        $this->set('data',$data);
        $this->set('nist',Configure::read('url.base'));
    }

    /**
     * View a particular system instance
     * @param $sysID
     * @param string $format
     */
	function view($sysID,$format="")
	{
        $data=$this->System->find('first', ['conditions'=>['System.sysID'=>$sysID],'recursive'=>1]);
        $citation=$this->Citation->find('first', ['conditions'=>['Citation.id'=>$data['Citation']['id']]]);
        if(isset($citation['Author'])&&$citation['Author']!="") {
            $data['Author']=$citation['Author'];
        } else {
            $data['Author']="No authors given";
        }
        if($format!="") { $this->export($data,$format); }
        $this->set('data',$data);
        $this->set('sysID',$sysID);
        $this->set('base',Configure::read('host.base'));
        $this->set('nist',Configure::read('url.base'));
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
        $input=$data['System'];
        $input['url']=$path."systems/view/".$input['sysID'];unset($input['id']);
        $input['refs']=json_decode($input['refs']);
        $input['source']=json_decode($input['source']);
        // Replace the volume data
        $input['volume']=$data['Volume'];unset($input['volume_id']);unset($input['volume']['id']);
        $input['volume']['url']=$path."volumes/view/".$data['Volume']['id'];
        // Replace the systemtype data
        $input['systemtype']=$data['Systemtype'];unset($input['systemtype_id']);unset($input['systemtype']['id']);
        // Replace the citation data
        $input['citation']=$data['Citation'];unset($input['citation_id']);unset($input['citation']['id']);unset($input['citation']['first']);
        $input['citation']['url']=$path."citations/view/".$data['Citation']['id'];
        // Add chemicals
        $input['chemicals']=[];
        foreach($data['Chemical'] as $c)
        {
            $temp=['name'=>$c['name'],'formula'=>$c['formula'],'casrn'=>$c['casrn'],'inchi'=>$c['inchi'],'inchikey'=>$c['inchikey'],'updated'=>$c['updated']];
            $temp['url']=$path."chemicals/view/".$c['id'];
            $input['chemicals'][]=$temp;
        }
        // Add variables
        $input['variables']=[];
        foreach($data['Variable'] as $v)
        {
            $temp=['name'=>$v['name'],'formula'=>$v['bounds']];
            $input['variables'][]=$temp;
        }
        // Add tables
        $input['tables']=[];
        foreach($data['Table'] as $t)
        {
            $temp=['content'=>json_decode($t['content'])];
            $input['tables'][]=$temp;
        }
        //echo "<pre>";print_r($input);echo "</pre>";exit;
        if($format=="xml")
        {
            $output="<?xml version='1.0'?>\n";
            $output.="<system>\n";
            foreach($input as $k1=>$v1)
            {
                if(is_numeric($k1)) { $k1="instance"; }
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
                                if(is_numeric($k3)) { $k3="instance"; }
                                $output.="<".$k3.">";
                                if(is_array($v3))
                                {
                                    foreach ($v3 as $k4=>$v4)
                                    {
                                        if(is_numeric($k4)) { $k4="instance"; }
                                        $output.="<".$k4.">";
                                        if(is_array($v4))
                                        {
                                            foreach($v4 as $k5=>$v5)
                                            {
                                                if(is_numeric($k5)) { $k5="instance"; }
                                                $output.="<".$k5.">".$v5."</".$k5.">";
                                            }
                                        }
                                        else
                                        {
                                            $output.=$v4;
                                        }
                                        $output.="</".$k4.">\n";
                                    }
                                }
                                else
                                {
                                    $output.=$v3;
                                }
                                $output.="</".$k3.">\n";
                            }
                        }
                        else
                        {
                            $output.=$v2;
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
            $output.="</system>";
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
?>