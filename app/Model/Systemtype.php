<?php
App::uses('AppModel', 'Model');

/**
 * Class Systemtype
 * Model for the systemtypes table
 */
class Systemtype extends AppModel
{
    // Link to tables via a one-to-many relationship
    public $hasMany = 'System';

    // Create virtual fields to return to views
    public $virtualFields=['first'=>'UPPER(SUBSTR(Systemtype.title,1,1))'];

}
