<?php

class DnsSlave extends Lxdb 
{
	static $__desc = array("", "",  "dnsslave",);
	static $__desc_nname =  array("h", "",  "slave_domain");
	static $__desc_master_ip =  array("", "",  "master_ip");
	static $__desc_syncserver =  array("", "",  "syncserver");
	static $__desc_serial =  array("", "",  "Serial");

	function isSync()
	{
		return false ;
	}

	static function createListNlist($parent, $view)
	{
		$nlist["parent_clname"] = "15%";
		$nlist["nname"] = "30%";
		$nlist["master_ip"] = "15%";
		$nlist["syncserver"] = "15%";
		$nlist["serial"] = "100%";

		return $nlist;
	}

	static function createListAlist($parent, $class)
	{
		$alist[] = "a=list&c=dnsslave";

		return $alist;
	}

	static function add($parent, $class, $param)
	{
		validate_domain_name($param['nname'], $bypass = true);
		validate_ipaddress($param['master_ip']);

		return $param;
	}

	function postAdd()
	{
		$ip = $this->master_ip;
		$domain = $this->nname;
		$syncserver = $this->syncserver;
		$path = "/opt/configs/dnsslave_tmp";

		if (!file_exists($path)) {
			lxshell_return("mkdir", "-p", $path);
		}

		exec("echo '{$ip}' > {$path}/{$domain}");
		exec("sh /script/fixdns --server={$syncserver} --nolog");
	}

	function deleteSpecific()
	{
		$ip = $this->master_ip;
		$domain = $this->nname;
	
		$syncserver = $this->syncserver;
		$path = "/opt/configs/dnsslave_tmp";

		if (!file_exists($path)) {
			lxshell_return("mkdir", "-p", $path);
		}

		exec("'rm' -rf {$path}/{$domain}");
		exec("sh /script/fixdns --server={$syncserver} --nolog");
	}

	static function AddListForm($parent, $class)
	{
		return self::addform($parent, $class);
	}

	static function addform($parent, $class, $typetd = null)
	{
		global $gbl, $sgbl, $login, $ghtml;

		$vlist['nname'] = null;
		$vlist['master_ip'] = null;
		$ret['variable'] = $vlist;
		$ret['action'] = 'add';
		
		return $ret;
	}
}
