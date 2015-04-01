<?php
App::uses('AppModel', 'Model');

/**
 * Class Volume
 */
class Volume extends AppModel
{
	
	public $hasMany = ['System'];
	
}