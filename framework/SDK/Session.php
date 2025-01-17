<?php
namespace framework;

class Session
{
	private $builder;
	private $options;
	private $user;
	
	function __construct($options, $id = '')
	{
		$this->builder = new QueryBuilder();
		$this->options = $options;
		$req = new Request();
		$this->user = $id;
		
		if($id == '')
			$this->user = $this->builder->select(
				$options['source'], 
				['session_user' => 'session_user']
			)->where(['session_key' => $req->cookie($options['key'])], '')->value();
	}
	
	function create()
	{
		$req = new Request();
		
		if(!$req->cookie($this->options['key'])){
			$sess_id = '';
			$chars = 'abcdefghijklmnoprsqtuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890';
			$len = strlen($chars);
	
			for($i=0;$i<10;$i++)
			{
				$sess_id .= substr($chars, rand(1, $len)-1, 1);
			}
			print_r($this->user->id);
			$res = $this->builder->insert(
				$this->options['source'], 
				['session_key' => $sess_id,'session_user' => $this->user->id]
			)->change();
			
			if(!$res)
				return false;
			
			setcookie($this->options['key'], $sess_id, ['httponly' => true]);
		}
	}
	
	function close()
	{
		$req = new Request();
		
		setcookie($this->options['key'], null, -1);
		
		$res = $this->builder->delete(
			$this->options['source']
		)->where(
			['session_key' => $req->cookie($this->options['key'])], 
			''
		)->change();
	}
	
	function get_user_id()
	{
		return $this->user;
	}
	
	function get_id()
	{
		return $this->sess_key;
	}
	
	function getLogin()
	{
		return $this->builder->select(
			'users', 
			['user_name' => 'user_name']
		)->where(['id' => $this->user], '')->value();
	}
	
	function getRole()
	{
		return $this->builder->select(
			'roles, users', 
			['role_name' =>'role_name']
		)->where(
			['users.id' => is_numeric($this->user)?$this->user:$this->user->id, 'roles.id' => ['user_role']], 
			'AND'
		)->value();
	}
	
	function getPermissions()
	{
		return $this->builder->select(
			'rules, users', 
			['rule_name' => 'rule_name']
		)->where(
			['Rule_role' => ['user_role'], 'users.id' => $this->user[0][0]], 
			'AND'
		)->all();
	}
	
	function addToken($token)
	{
		return setcookie('csrf_token', $token, ['httponly' => true, 'expires' => time()+1800]);
	}
	
	function checkToken($token)
	{
		return $token == (new Request())->cookie('csrf_token');
	}
}
?>