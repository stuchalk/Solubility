<?php
App::uses('AppModel', 'Model');

/**
 * Class Citation
 * Model for the citations table
 */
class Citation extends AppModel
{
    // Link to systems via a one-to-many relationship
    public $hasMany='System';

    // Link to authors via a many-to-many relationship
    public $hasAndBelongsToMany = [
		'Author' => [
				'className' => 'Author',
				'joinTable' => 'authors_citations',
				'foreignKey' => 'citation_id',
				'associationForeignKey' => 'author_id',
				'unique' => true]
		];

    // Create virtual fields to return to views
    public $virtualFields = [
        'cite'  => 'CONCAT(Citation.journal," ",Citation.year,", ",Citation.volume,", ",Citation.firstpage)',
        'first' => 'UPPER(SUBSTR(Citation.journal,1,1))'
    ];
	
}