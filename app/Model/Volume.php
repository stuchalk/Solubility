<?php
App::uses('AppModel', 'Model');

/**
 * Class Volume
 * Model for the volumes table
 */
class Volume extends AppModel
{
    // Link to systems via a one-to-many relationship
    public $hasMany = ['System'=>['order'=>'sysID']];
	
}