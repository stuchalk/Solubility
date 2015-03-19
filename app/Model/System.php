<?php

App::uses('AppModel', 'Model');
class System extends AppModel
{

	public $belongsTo = array('Citation','Systemtype');
	
	public $hasAndBelongsToMany = array(
		'Chemical' =>
			array(
				'className' => 'Chemical',
				'joinTable' => 'chemicals_systems',
				'foreignKey' => 'system_id',
				'associationForeignKey' => 'chemical_id',
				'unique' => true
				)
		);
		
	public $hasMany = array('Variable','Table');
	
}