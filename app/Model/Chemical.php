<?php
App::uses('AppModel', 'Model');

/**
 * Class Chemical
 * Model for the chemicals table
 */
class Chemical extends AppModel
{
    // Link to systems via a many-to-many relationship
	public $hasAndBelongsToMany = [
		'System' => [
				'className' => 'System',
				'joinTable' => 'chemicals_systems',
				'foreignKey' => 'chemical_id',
				'associationForeignKey' => 'system_id',
				'unique' => true,
                'order' => 'title']
		];

    // Create virtual fields to return to views
    public $virtualFields=['first' => 'UPPER(SUBSTR(Chemical.name,1,1))'];

}