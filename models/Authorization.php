<?php
namespace models;

use framework\Request;
use framework\Session;
use framework\Validator;

class Authorization
{
	private $request;
	public $user;
	private $password;
	public $remember;
	private $db;
	private $model;
	
	function __construct($req, $db)
	{
		$this->model = new User($db);
		$this->request = $req;
		$this->user = $this->request->post('User');
		$this->password = $this->request->post('Password');
		$this->remember = $this->request->post('Remember');
		$this->db = $db;
	}
	
	function getUserByToken()
	{
		return $this->model->read(['user_name'], ['user_remember' => $this->request->cookie('token')])[0];
	}
	
	function setUserToken()
	{
		$token = '';
		$chars = 'abcdefghijklmnoprsqtuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890';
		$len = strlen($chars);

		for($i=0;$i<10;$i++)
		{
			$token .= substr($chars, rand(1, $len)-1, 1);
		}
		
		$res = $this->model->update(['user_remember', $token], ['user_name' => $this->user]);
		if($res)
			setcookie('token', $token);
	}
	
	function validate($data)
	{
		$valid = new Validator($data);
		
		return $valid->sql('User') or $valid->sql('Password');
	}
	
	function isAdmin($conf)
	{
		$sess = new Session($this->db, $conf, $this->user);
		$sess->create();
		
		return strcmp($sess->getRole(), 'Admin') == 0;
	}
	
	function Execute($cfg)
	{
		$usr = null;
		if($this->request->cookie($cfg['key']))
		{
			$sess = new Session($this->db, $cfg);
			$usr = $sess->get_user_id();
			$this->user = $usr;
			
			return ($usr != null);
		}
		if(is_null($usr))
		{
			$this->user = $this->model->read(
				['id'], 
				['user_name' => $this->user, 'user_password' => md5($this->password)]);
			return ($this->user != null);
		} else return false;
	}
}
?>