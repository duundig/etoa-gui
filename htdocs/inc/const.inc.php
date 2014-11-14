<?php

	// OS-Version feststellen
	if (defined('POSIX_F_OK'))
	{
		define('UNIX',true);
		define('WINDOWS',false);
	}
	else
	{
		define('UNIX',false);
		define('WINDOWS',true);
	}
	
	/***********
	* Database *
	***********/
	define("WINDOWS_MYSQL_PATH", "c:\\xampp\\mysql\\bin\\mysql.exe");
	define("WINDOWS_MYSQLDUMP_PATH", "c:\\xampp\\mysql\\bin\\mysqldump.exe");

	// Cache directory
	if (!defined('CACHE_ROOT'))
		define('CACHE_ROOT',RELATIVE_ROOT.'cache');
	
	// Log directory
	if (!defined('LOG_DIR'))
		define('LOG_DIR',RELATIVE_ROOT.'log');

	// Class directory
	if (!defined('CLASS_ROOT'))
		define('CLASS_ROOT',RELATIVE_ROOT.'classes');

	// Data file directory
	if (!defined('DATA_DIR'))
		define('DATA_DIR',RELATIVE_ROOT."data");

	// Image directory
	if (!defined('IMAGE_DIR'))
		define('IMAGE_DIR',RELATIVE_ROOT."images");

	// Smarty Path
	define('SMARTY_DIR', RELATIVE_ROOT."libs/smarty/");
	define('SMARTY_TEMPLATE_DIR', CACHE_ROOT."/smarty_templates");
	define('SMARTY_COMPILE_DIR', CACHE_ROOT."/smarty_compile");

	// xAjax
	define('XAJAX_DIR',RELATIVE_ROOT."libs/xajax");


	if (!defined('ADMIN_MODE'))
		define('ADMIN_MODE',false);

	define('ERROR_LOGFILE', LOG_DIR."/errors.log");
	define('DBERROR_LOGFILE', LOG_DIR."/dberrors.log");

	define("DEVCENTER_PATH","http://dev.etoa.ch");	// Entwickler Link
	define("DEVCENTER_ONCLICK","window.open('".DEVCENTER_PATH."','dev','width=1024,height=768,scrollbars=yes');");	// Entwickler Link
?>
