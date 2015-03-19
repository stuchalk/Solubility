<?php

App::uses('AppModel', 'Model');
class Systemtype extends AppModel
{
	public $hasMany = 'System';
}
