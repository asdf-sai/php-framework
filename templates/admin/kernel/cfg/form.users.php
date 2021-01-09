<?php
$name = "admin\users";
$template = "form.users";
$target = "index.php?page=admin\users";
$modules = [
	'header' => [
		'size' => '1',
		'class' => 'margin',
		'id' => 'CaptionText',
		'align' => 'center',
		'text' => 'Edit user'
	],
	'form' => [
		'target' => 'index.php?page=admin\\\\users',
		'method' => 'POST',
		'id' => 'editor_form',
		'class' => 'UserEditor',
		'fields' => [
			'User_name' => [
				'field_type' => 'edit',
				'id' => 'user_name',
				'class' => 'user-name flex-row',
				'name' => 'name'
			],
			'User_role' => [
				'field_type' => 'edit',
				'id' => 'user_role',
				'class' => 'user-role flex-row',
				'name' => 'role'
			],
			'submit' => [
				'field_type' => 'send',
				'text' => 'send',
				'class' => 'Submit flex-col',
				'id' => 'SubmitBtn'
			]
		]
	],
	'link' => [
		'WebMaster' => [
			'target' => 'mailto: asvelat@gmail.com',
			'class' => 'WebMasterMail',
			'id' => 'MailAddress',
			'name' => 'webmaster'
		]
	],
];
?>