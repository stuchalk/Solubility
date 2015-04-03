<?php

/**
 * Class AuthorsController
 * Methods to access authors (authors of the papers that the solubility data comes from)
 */
class AuthorsController extends AppController
{

    /**
     * Return all authors in the database
     */
    function index()
	{
		$data=$this->Author->find('list', ['fields'=>['id','name','first'],'order'=>['first','name']]);
        $this->set('data',$data);
        $this->set('nist',Configure::read('url.base'));
	}

    /**
     * View a specific author
     * @param $id
     * @param string $format
     */
    function view($id,$format="")
	{
		$data=$this->Author->find('first', ['conditions'=>['Author.id'=>$id],'recursive'=>1]);
        if($format!="") { $this->export($data,$format); }
        $this->set('data',$data);
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
        $output=$data['Author'];unset($output['first']);
        $output['url']=$path."authors/view/".$output['id'];unset($output['id']);
        // Add citations
        $output['citations']=[];
        foreach($data['Citation'] as $c)
        {
            unset($c['AuthorsCitation']);unset($c['first']);
            $c['url']=$path."citations/view/".$c['id'];unset($c['id']);
            $output['citations'][]=$c;
        }
        // Output data
        if($format=="xml")
        {
            $this->Export->xml($output['lastname'],"author",$output);
        }
        elseif($format=="json")
        {
            $this->Export->json($output['lastname'],"author",$output);
        }
        elseif($format=="jsonld")
        {
            $context=[
                "firstname"=>[
                    "@id"=>"http://xmlns.com/foaf/0.1/givenName",
                    "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                ],
                "title"=>[
                    "@id"=>"http://xmlns.com/foaf/0.1/familyName",
                    "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                ],
                "updated"=>[
                    "@id"=>"http://purl.org/dc/terms/modified",
                    "@type"=>"http://www.w3c.org/2001/XMLSchema#dateTime"
                ],
                "name"=>[
                    "@id"=>"http://xmlns.com/foaf/0.1/givenName",
                    "@type"=>"http://www.w3c.org/2001/XMLSchema#string"
                ],
                "url"=>[
                    "@id"=>"http://schema.org/url",
                    "@type"=>"@id"
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
            ];
            $this->Export->jsonld($output['lastname'],"system",$output,$context);
        }
        else
        {
            return;
        }
    }

}