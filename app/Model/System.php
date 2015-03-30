<?php
App::uses('AppModel', 'Model');

/**
 * Class System
 */
class System extends AppModel
{
	public $belongsTo = ['Citation','Systemtype','Volume'];
	
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
		
	public $hasMany = ['Variable','Table'];
	
}