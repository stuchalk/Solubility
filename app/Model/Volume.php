<?php

App::uses('AppModel', 'Model');
class Volume extends AppModel
{
	
	public $hasMany = array('System');
	
}