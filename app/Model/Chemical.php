<?php

App::uses('AppModel', 'Model');
class Chemical extends AppModel
{

	public $hasAndBelongsToMany = array(
		'System' =>
			array(
				'className' => 'System',
				'joinTable' => 'chemicals_systems',
				'foreignKey' => 'chemical_id',
				'associationForeignKey' => 'system_id',
				'unique' => true
				)
		);

}