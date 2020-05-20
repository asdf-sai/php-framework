<?php
include_once "config\\settings.php";
$files = [scandir("templates\\default\\kernel\\cfg"), scandir("controllers")];

function str_rand()
{
	$str = '';
	$chars = 'abcdefghijklmnoprsqtuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890';
	$len = strlen($chars);
	
	for($i=0;$i<10;$i++)
	{
		$str .= substr($chars, rand(1, $len)-1, 1);
	}
	
	return $str;
}

function fileStorage($source, $files)
{
	$aliases = fopen("aliases\\$source".".php","r");
	
	$data = array();
	
	for($i=0;$i<count($files[0]);$i++)
	{
		if(!is_dir($files[0][$i]))
		{
			include_once "views\\page\\cfg\\".$files[0][$i];
			
			if(isset($target))
			{
				array_push($data, $target);
			}
			
			foreach($modules as $value)
			{
				if(key_exists('target', $value))
				{
					array_push($data, $value['target']);
				}
			}
		}
	}
	
	for($i=0;$i<count($files[1]);$i++)
	{
		if(!is_dir($files[1][$i]))
		{
			$file = fopen("controllers\\".$files[1][$i],"r");
			while(!feof($file))
			{
				$str = fgets($file);
				if(strpos($str, 'header'))
				{
					$alias = str_replace("');", '', split(':', $str)[1]);
					array_push($data, $alias);
				}
			}
			
			fclose($file);
		}
	}
	
	array_unique($data);
	
	foreach($data as $value)
	{
		fwrite($aliases, "$value=>index.php?alias=".str_rand().";");
	}
	
	fclose($aliases);
}

function tableStorage($source, $files, $ConData)
{
	include_once "framework\\SDK\\DB.php";
	
	$db = new framework\DB($ConData);
	
	for($i=0;$i<count($files[0]);$i++)
	{
		echo $files[0][$i].'<br>';
		if(!is_dir($files[0][$i]))
		{
			include_once "templates\\default\\kernel\\cfg\\".$files[0][$i];
			
			if(isset($target))
			{
				$res = $db->ValueQuery("SELECT id FROM $source WHERE page=\"$target\"");
					
				if($res == null)
				{
					$res = $db->ChangeQuery("INSERT INTO $source(name, page) VALUES(\"index.php?alias=".str_rand()."\",\"$target\")");
				}
			}
			
			foreach($modules as $value)
			{
				if(key_exists('target', $value))
				{
					$res = $db->ValueQuery("SELECT id FROM $source WHERE page=\"".$value['target']."\"");
					
					if($res == null)
					{
						$res = $db->ChangeQuery("INSERT INTO $source(name, page) VALUES(\"index.php?alias=".str_rand()."\",\"".$value['target']."\")");
					}
				}
			}
		}
	}
	
	for($i=0;$i<count($files[1]);$i++)
	{
		echo $files[1][$i].'<br>';
		if(!is_dir($files[1][$i]))
		{
			$file = fopen("controllers\\".$files[1][$i],"r");
			while(!feof($file))
			{
				$str = fgets($file);
				
				if(strpos($str, '$this->toPage'))
				{
					
					$alias = trim(str_ireplace("('", "", str_ireplace("');", "", explode('toPage', $str)[1])));
					
					if(strpos($alias, 'page')) $alias = explode('=', $alias)[1];
					
					$res = $db->ValueQuery("SELECT id FROM $source WHERE page=\"$alias\"");
					
					if($res == null)
					{
						$res = $db->ChangeQuery("INSERT INTO $source(name, page) VALUES(\"index.php?alias=".str_rand()."\",\"$alias\")");
					}
				}
			}
			
			fclose($file);
		}
	}
	echo 'finish';
}

switch($alias['storage'])
{
	case 'file': fileStorage($alias['source'], $files);break;
	case 'table': tableStorage($alias['source'], $files, $database);break;
}
?>