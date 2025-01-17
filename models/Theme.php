<?php
namespace models;

use framework\Model;
use framework\Editor;

class Theme extends Model
{
	use editor;
	
	function getUser($id)
	{
		$model = new \models\User();
		if($id != 0)
			return $model->read(['name' => 'User_name'], ['id' => $id])->name;
		else
			return $model->read(['name' => 'User_name'])->name;
	}
	
	function getForum($id)
	{
		$model = new \models\Forum();
		if($id != 0)
			return $model->read(['name' => 'name'], ['id' => $id])->name;
		else
			return $model->read(['name' => 'name'])->name;
	}
}
?>