<?php

$configArray = parse_ini_file('../../app/config/config.ini', true);
/**
 * Your database authentication information goes here
 * @see http://dbv.vizuina.com/documentation/
 */
define('DB_HOST', $configArray['database']['localhost']);
//define('DB_PORT', 80);
define('DB_USERNAME', $configArray['database']['username']);
define('DB_PASSWORD', $configArray['database']['password']);
define('DB_NAME', $configArray['database']['dbname']);

/**
 * Authentication data for access to DBV itself
 * If you leave any of the two constants below blank, authentication will be disabled
 * @see http://dbv.vizuina.com/documentation/#optional-settings
 */
define('DBV_USERNAME', '');
define('DBV_PASSWORD', '');

/**
 * @see http://dbv.vizuina.com/documentation/#writing-adapters
 */
define('DB_ADAPTER', 'MySQL');

define('DS', DIRECTORY_SEPARATOR);
define('DBV_ROOT_PATH', dirname(__FILE__));

define('DBV_TMP_PATH', '../../db/');

/**
 * Only edit this lines if you want to place your schema files in custom locations
 * @see http://dbv.vizuina.com/documentation/#optional-settings
 */
define('DBV_DATA_PATH', DBV_TMP_PATH . DS . 'data');
define('DBV_SCHEMA_PATH', DBV_TMP_PATH . DS . 'schema');
define('DBV_REVISIONS_PATH', DBV_TMP_PATH . DS . 'revisions');
define('DBV_META_PATH', DBV_TMP_PATH . DS . 'meta');


ini_set('magic_quotes_gpc', 'Off');
error_reporting(E_ALL ^ E_NOTICE);


/**
 * I18n support
 */

define('DBV_LANGUAGES_PATH', DBV_ROOT_PATH . DS . 'languages');
define('DEFAULT_LOCALE', 'en_US');
define('DEFAULT_ENCODING', 'UTF-8');
define('DEFAULT_DOMAIN', 'default');

putenv("LC_ALL=".DEFAULT_LOCALE);
setlocale(LC_ALL, DEFAULT_LOCALE);

bindtextdomain(DEFAULT_DOMAIN, DBV_LANGUAGES_PATH);
bind_textdomain_codeset(DEFAULT_DOMAIN, DEFAULT_ENCODING);
textdomain(DEFAULT_DOMAIN);