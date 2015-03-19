<?php

App::uses('AppModel', 'Model');
class Author extends AppModel
{
	
	public $hasAndBelongsToMany = array(
		'Citation' =>
			array(
				'className' => 'Citation',
				'joinTable' => 'authors_citations',
				'foreignKey' => 'author_id',
				'associationForeignKey' => 'citation_id',
				'unique' => true
				)
		);

}