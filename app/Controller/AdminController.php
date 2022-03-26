<?php

/**
 * Class AdminController
 */
class AdminController extends AppController
{

    public $uses = false;

    /**
     * beforeFilter function
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     * Proxy server for http accessible files that need to be loaded in https
     * Used for e.g. mol files loaded into jsmol
     * Format https://chalk.coas.unf.edu/sol/admin/proxy?url=<URL>
     */
    public function proxy()
    {
        $url=$this->request->query['url'];
        $h=get_headers($url,true);
        header('Content-Type: '.$h['Content-Type']);
        $f=file_get_contents($url);
        echo $f;
        exit;
    }
}