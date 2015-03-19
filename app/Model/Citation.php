<?php
App::uses('AppModel', 'Model');

/**
 * Class Citation
 * Model for the citations table
 */
class Citation extends AppModel
{
	public $hasMany='System';
	
	public $hasAndBelongsToMany = [
		'Author' =>
			[
				'className' => 'Author',
				'joinTable' => 'authors_citations',
				'foreignKey' => 'citation_id',
				'associationForeignKey' => 'author_id',
				'unique' => true
				]
		];

	public $virtualFields = [
        'cite'  => 'CONCAT(Citation.journal," ",Citation.year,", ",Citation.volume,", ",Citation.firstpage)',
        'first' => 'UPPER(SUBSTR(Citation.journal,1,1))'
        ];
	
}