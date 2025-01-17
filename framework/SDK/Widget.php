<?php
namespace framework;

class Widget
{
	public $cfg;
	public $positions = ['content'];
	
	function __construct($cfg)
	{
		$this->cfg = $cfg;
	}
	
	function plugin($key, $value, $cfg)
	{
		$name = '\\Plugins\\'.ucfirst($key);
		$plugin = new $name(['value' => $value, 'db' => DB::getInstance(), 'cfg' => $cfg]);
		
		return $plugin->show();
	}
	
	function loadContent($cfg)
	{
		$content = '';
		
		foreach($this->positions as $position){
			foreach($this->cfg[$position] as $key => $param){
				$html = '';
				
				if(in_array($key, $cfg->GetSetting('plugins')))
					$values = $this->plugin($key, $param, $cfg);
				
				$path = $_SERVER['DOCUMENT_ROOT'].$cfg->GetSetting('base').DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.$cfg->GetSetting('site_template').DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$key.'.html';
				$html = file_get_contents($path);
				
				foreach($param as $key => $value)
					$html = str_ireplace('{'.$key.'}', $value, $html);
				
				$content .= $html;
			}
		}
		
		$this->cfg[$position] = $content;
	}
	
	function show($mods)
	{
		$name = strtolower(str_ireplace('Widget', '',explode('\\', get_called_class())[1]));
		$mods[$name] = $this->cfg;
		
		return $mods;
	}
}
?>