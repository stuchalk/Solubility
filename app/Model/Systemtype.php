<?php
App::uses('AppModel', 'Model');

/**
 * Class Systemtype
 */
class Systemtype extends AppModel
{

    public $hasMany = 'System';

    public $virtualFields=['first'=>'UPPER(SUBSTR(Systemtype.title,1,1))'];

}
