<?php

App::uses('AppModel', 'Model');
class Chemical extends AppModel
{
	public $hasAndBelongsToMany = [
		'System' =>
			[
				'className' => 'System',
				'joinTable' => 'chemicals_systems',
				'foreignKey' => 'chemical_id',
				'associationForeignKey' => 'system_id',
				'unique' => true,
                'order' => 'title'
				]
		];

    public $virtualFields=['first' => 'UPPER(SUBSTR(Chemical.name,1,1))'];

}