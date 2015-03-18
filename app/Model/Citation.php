<?php

App::uses('AppModel', 'Model');
class Citation extends AppModel
{
	
	public $hasMany='System';
	
	public $hasAndBelongsToMany = array(
		'Author' =>
			array(
				'className' => 'Author',
				'joinTable' => 'authors_citations',
				'foreignKey' => 'citation_id',
				'associationForeignKey' => 'author_id',
				'unique' => true
				)
		);

	public $virtualFields = array('cite' => 'CONCAT(Citation.journal," ",Citation.year,", ",Citation.volume,", ",Citation.firstpage)');
	
}