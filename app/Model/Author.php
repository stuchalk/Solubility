<?php
App::uses('AppModel', 'Model');

/**
 * Class Author
 * Model for the authors table
 */
class Author extends AppModel
{
    // Link to citations via a many-to-many relationship
    public $hasAndBelongsToMany = [
		'Citation' => [
				'className' => 'Citation',
				'joinTable' => 'authors_citations',
				'foreignKey' => 'author_id',
				'associationForeignKey' => 'citation_id',
				'unique' => true]
		];

    // Create virtual fields to return to views
    public $virtualFields=[
        'first'=>'UPPER(SUBSTR(Author.lastname,1,1))',
        'name'=>'CONCAT(Author.firstname," ",Author.lastname)'
    ];

}