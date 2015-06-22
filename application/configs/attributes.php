<?php
global $_INPUT_TYPES;

$_INPUT_TYPES = array(
	'text' => array(
				'id'	=> 'text',
				'name'	=> 'Text Field',
				'data_type' => 'text'
	),
	'varchar' => array(
				'id'	=> 'varchar',
				'name'	=> 'Var char',
				'data_type' => 'varchar'
	),
	'checkbox'	=> array(
			'id'	=> 'checkbox',
			'name'	=> 'Check box',
			'data_type' => 'int'
	),
	'yesno'	=> array(
				'id'	=> 'yesno',
				'name'	=> 'Yes/No',
				'data_type' => 'int'
	),										
	'dropdown'	=> array(
				'id'	=> 'dropdown',
				'name'	=> 'Dropdown',
				'data_type' => 'int'				
	),
	'multiple'	=> array(
				'id'	=> 'multiple',
				'name'	=> 'Multiple',
				'data_type' => 'int'				
	)
);
