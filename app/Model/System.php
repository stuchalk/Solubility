<?php
App::uses('AppModel', 'Model');

/**
 * Class System
 * Model for the systems table
 */
class System extends AppModel
{
    // Link to tables via a one-to-one relationship
    public $belongsTo = ['Citation','Systemtype','Volume'];

    // Link to tables via a many-to-many relationship
    public $hasAndBelongsToMany = [
		'Chemical' =>
			[
				'className' => 'Chemical',
				'joinTable' => 'chemicals_systems',
				'foreignKey' => 'system_id',
				'associationForeignKey' => 'chemical_id',
				'unique' => true
				]
		];

    // Link to tables via a one-to-many relationship
    public $hasMany = ['Variable','Table'];
	
}