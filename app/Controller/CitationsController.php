<?php

/**
 * Class ChemicalsController
 * Methods to access citations
 */
class CitationsController extends AppController
{

    /**
     * Return all citations in the database
     */
	function index()
	{
		$data=$this->Citation->find('list', ['fields'=>['id','cite','first'],'order'=>['first','cite']]);
        $this->set('data',$data);
        $this->set('nist',Configure::read('url.base'));
	}

    /**
     * View a specific citation
     * @param $id
     * @param string $format
     */
    function view($id,$format="")
	{
        $data=$this->Citation->find('first', ['conditions'=>['Citation.id'=>$id],'recursive'=>1]);
        if($format!="") { $this->export($data,$format); }
        $this->set('data',$data);
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
        $output=$data['Citation'];unset($output['first']);
        $output['url']=$path."citations/view/".$output['id'];unset($output['id']);
        // Add system data
        $output['systems']=[];
        foreach($data['System'] as $s) {
            $output['systems'][]=['sysID'=>$s['sysID'],'title'=>$s['title'],'url'=>$path.'systems/view/'.$s['sysID']];
        }
        // Output data
        if($format=="xml") {
            $this->Export->xml("","citation",$output);
        } elseif($format=="json") {
            $this->Export->json("","citation",$output);
        } elseif($format=="jsonld") {
            $context=[
                "title" => [
                    "@id" => "http://purl.org/dc/terms/title",
                    "@type" => "http://www.w3c.org/2001/XMLSchema#string"
                ],
                "journal" => [
                    "@id" => "http://purl.org/dc/terms/source",
                    "@type" => "http://www.w3c.org/2001/XMLSchema#string"
                ],
                "year" => [
                    "@id" => "http://purl.org/dc/terms/date",
                    "@type" => "http://www.w3c.org/2001/XMLSchema#string"
                ],
                "volume" => [
                    "@id" => "http://prismstandard.org/namespaces/1.2/basic/volume",
                    "@type" => "http://www.w3c.org/2001/XMLSchema#string"
                ],
                "issue" => [
                    "@id" => "http://prismstandard.org/namespaces/1.2/basic/issue",
                    "@type" => "http://www.w3c.org/2001/XMLSchema#string"
                ],
                "firstpage" => [
                    "@id" => "http://prismstandard.org/namespaces/1.2/basic/startingPage",
                    "@type" => "http://www.w3c.org/2001/XMLSchema#string"
                ],
                "lastpage" => [
                    "@id" => "http://prismstandard.org/namespaces/1.2/basic/endingPage",
                    "@type" => "http://www.w3c.org/2001/XMLSchema#string"
                ],
                "doi" => [
                    "@id" => "http://purl.org/dc/terms/identifier",
                    "@type" => "http://www.w3c.org/2001/XMLSchema#string"
                ],
                "url" => [
                    "@id" => "http://schema.org/url",
                    "@type" => "@id"
                ],
                "updated" => [
                    "@id" => "http://purl.org/dc/terms/modified",
                    "@type" => "http://www.w3c.org/2001/XMLSchema#dateTime"
                ],
                "cite" => [
                    "@id" => "http://purl.org/dc/terms/bibliographicCitation",
                    "@type" => "http://www.w3c.org/2001/XMLSchema#string"
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
            $this->Export->jsonld("","citation",$output,$context);
        } else {
            return;
        }
    }

}