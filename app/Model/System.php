<?php
App::uses('AppModel', 'Model');

/**
 * Class System
 * Model for the systems table
 */
class System extends AppModel
{
    // Link to citations, systemtypes, and volumes via a one-to-one relationship
    public $belongsTo = ['Citation','Systemtype','Volume'];

    // Link to varaibles and tables via a one-to-many relationship
    public $hasMany = ['Variable','Table'];

    // Link to chemicals via a many-to-many relationship
    public $hasAndBelongsToMany = [
		'Chemical' => [
				'className' => 'Chemical',
				'joinTable' => 'chemicals_systems',
				'foreignKey' => 'system_id',
				'associationForeignKey' => 'chemical_id',
				'unique' => true]
		];

    // Create virtual fields to return to views
    public $virtualFields=['first' => 'UPPER(SUBSTR(System.title,1,1))'];

}