<?php
App::uses('AppModel', 'Model');

/**
 * Class Volume
 * Model for the volumes table
 */
class Volume extends AppModel
{
    // Link to tables via a one-to-many relationship
    public $hasMany = ['System'];
	
}