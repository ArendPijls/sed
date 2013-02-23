<?php

/**
 * Spoon configuration
 */
// should the debug information be shown
define('SPOON_DEBUG', !isProductionSite());
// mailaddress where the exceptions will be mailed to (<tag>-bugs@fork-cms.be)
define('SPOON_DEBUG_EMAIL', isProductionSite() ? 'info@sameneendak.be' : '');
// message for the visitors when an exception occur
define('SPOON_DEBUG_MESSAGE', 'Internal error.');
// default charset used in spoon.
define('SPOON_CHARSET', 'utf-8');


/**
 * Fork configuration
 */
// version of Fork
define('FORK_VERSION', '3.4.4');


/**
 * Database configuration
 */
if(isProductionSite())
{
	exit('insert production config');
}

else
{
	define('DB_TYPE', 'mysql');
	define('DB_DATABASE', 'admin_sed');
	define('DB_HOSTNAME', 'criminalwar.us');
	define('DB_PORT', '3306');
	define('DB_USERNAME', 'admin_sed');
	define('DB_PASSWORD', 'fr8UfODt');
}


/**
 * Site configuration
 */
// the protocol
define('SITE_PROTOCOL', isset($_SERVER['SERVER_PROTOCOL']) ? (strpos(strtolower($_SERVER['SERVER_PROTOCOL']), 'https') === false ? 'http' : 'https') : 'http');
// the domain (without http(s))
define('SITE_DOMAIN', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'sed.dev');
// the default title
define('SITE_DEFAULT_TITLE', 'Fork CMS');
// the url
define('SITE_URL', SITE_PROTOCOL . '://' . SITE_DOMAIN);
// is the site multilanguage?
define('SITE_MULTILANGUAGE', false);
// default action group tag
define('ACTION_GROUP_TAG', '@actiongroup');
// default action rights level
define('ACTION_RIGHTS_LEVEL', '7');


/*
 * Path configuration
 */
define('PATH_WWW', dirname(__FILE__) . '/..');
define('PATH_LIBRARY', dirname(__FILE__));


/**
 * @return bool Whether or not we're running the site in production.
 */
function isProductionSite()
{
	static $productionDomains = array('www.sameneendak.be', 'sameneendak.be', 'www.sameneendak.nl', 'sameneendak.nl');
	return in_array(SITE_DOMAIN, $productionDomains);
}
