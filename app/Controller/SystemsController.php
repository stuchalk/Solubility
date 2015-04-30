<?php

/**
 * Class SystemsController
 * Methods to access systems
 */
class SystemsController extends AppController
{
	public $uses = ['System','Citation'];

    /**
     * Return all systems in the database
     */
    public function index()
    {
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
        $output=$data['System'];unset($output['first']);
        $output['url']=$path."systems/view/".$output['sysID'];unset($output['id']);
        $output['refs']=json_decode($output['refs']);
        $output['source']=json_decode($output['source']);
        // Replace the volume data
        $output['volume']=$data['Volume'];unset($output['volume_id']);unset($output['volume']['id']);
        $output['volume']['url']=$path."volumes/view/".$data['Volume']['id'];
        // Replace the systemtype data
        $output['systemtype']=$data['Systemtype'];unset($output['systemtype_id']);unset($output['systemtype']['id']);unset($output['systemtype']['first']);
        // Replace the citation data
        $output['citation']=$data['Citation'];unset($output['citation_id']);unset($output['citation']['id']);unset($output['citation']['first']);
        $output['citation']['url']=$path."citations/view/".$data['Citation']['id'];
        // Add chemicals
        $output['chemicals']=[];
        foreach($data['Chemical'] as $c)
        {
            $temp=['name'=>$c['name'],'formula'=>$c['formula'],'casrn'=>$c['casrn'],'inchi'=>$c['inchi'],'inchikey'=>$c['inchikey'],'updated'=>$c['updated']];
            $temp['url']=$path."chemicals/view/".$c['id'];
            $output['chemicals'][]=$temp;
        }
        // Add variables
        $output['variables']=[];
        foreach($data['Variable'] as $v)
        {
            $temp=['name'=>$v['name'],'formula'=>$v['bounds']];
            $output['variables'][]=$temp;
        }
        // Add tables
        $output['tables']=[];
        foreach($data['Table'] as $t)
        {
            $temp=['content'=>json_decode($t['content'])];
            $output['tables'][]=$temp;
        }
        // Output data
        if($format=="xml")
        {
            $this->Export->xml("","system",$output);
        }
        elseif($format=="json")
        {
            $this->Export->json("","system",$output);
        }
        elseif($format=="jsonld")
        {
            $context=[
                "sysID"=>[
                        "@id"=>"http://purl.org/dc/terms/identifier",
                        "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                    ],
                    "title"=>[
                        "@id"=>"http://purl.org/dc/terms/title",
                        "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                    ],
                    "preparer"=>[
                        "@id"=>"http://purl.org/dc/terms/creator",
                        "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                    ],
                    "remarks"=>[
                        "@id"=>"http://chalk.coas.unf.edc/solubility/remarks",
                        "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                    ],
                    "data"=>[
                        "@id"=>"http://chalk.coas.unf.edc/solubility/data",
                        "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                    ],
                    "datanotes"=>[
                        "@id"=>"http://chalk.coas.unf.edc/solubility/notes",
                        "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                    ],
                    "method"=>[
                        "@id"=>"http://chalk.coas.unf.edc/solubility/method",
                        "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                    ],
                    "source"=>[
                        "@id"=>"http://chalk.coas.unf.edc/solubility/source",
                        "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                    ],
                    "errors"=>[
                        "@id"=>"http://chalk.coas.unf.edc/solubility/error",
                        "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                    ],
                    "refs"=>[
                        "@id"=>"http://purl.org/dc/terms/bibliographicCitation",
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
                    "volume"=>[
                        "nistid"=>[
                            "@id"=>"http://purl.org/dc/terms/identifier",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "vol"=>[
                            "@id"=>"http://prismstandard.org/namespaces/1.2/basic/volume",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "title"=>[
                            "@id"=>"http://purl.org/dc/terms/title",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "reference"=>[
                            "@id"=>"http://purl.org/dc/terms/bibliographicCitation",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "url"=>[
                            "@id"=>"http://schema.org/url",
                            "@type"=>"@id"
                        ],
                        "updated"=>[
                            "@id"=>"http://purl.org/dc/terms/modified",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#dateTime"
                        ]
                    ],
                    "systemtype"=>[
                        "sysID"=>[
                            "@id"=>"http://purl.org/dc/terms/identifier",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "title"=>[
                            "@id"=>"http://purl.org/dc/terms/title",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "updated"=>[
                            "@id"=>"http://purl.org/dc/terms/modified",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#dateTime"
                        ]
                    ],
                    "citation"=>[
                        "title"=>[
                            "@id"=>"http://purl.org/dc/terms/title",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "journal"=>[
                            "@id"=>"http://purl.org/dc/terms/source",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "year"=>[
                            "@id"=>"http://purl.org/dc/terms/date",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "volume"=>[
                            "@id"=>"http://prismstandard.org/namespaces/1.2/basic/volume",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "issue"=>[
                            "@id"=>"http://prismstandard.org/namespaces/1.2/basic/issue",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "firstpage"=>[
                            "@id"=>"http://prismstandard.org/namespaces/1.2/basic/startingPage",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "lastpage"=>[
                            "@id"=>"http://prismstandard.org/namespaces/1.2/basic/endingPage",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "doi"=>[
                            "@id"=>"http://purl.org/dc/terms/identifier",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "url"=>[
                            "@id"=>"http://schema.org/url",
                            "@type"=>"@id"
                        ],
                        "updated"=>[
                            "@id"=>"http://purl.org/dc/terms/modified",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#dateTime"
                        ],
                        "cite"=>[
                            "@id"=>"http://purl.org/dc/terms/bibliographicCitation",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ]
                    ],
                    "chemicals"=>[
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
                        ]
                    ],
                    "variables"=>[
                        "name"=>[
                            "@id"=>"http://purl.org/dc/terms/identifier",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ],
                        "formula"=>[
                            "@id"=>"http://chalk.coas.unf.edc/solubility/formula",
                            "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                        ]
                    ],
                    "tables"=>["content"=>[
                        "@id"=>"http://chalk.coas.unf.edc/solubility/table",
                        "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                    ]]
            ];
            $this->Export->jsonld("","system",$output,$context);
        }
        else
        {
            return;
        }
    }

}