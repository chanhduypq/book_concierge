<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['module_config'] = array(
	'description'	=> 'Your module description',
	'name'			=> 'localization',
	'version'		=> '0.0.1',
	'author'		=> 'admin'
);

$config['localization_countries_cache'] = dirname(dirname(__FILE__)).'/cache/countries.txt';
$config['localization_currency_cache'] = dirname(dirname(__FILE__)).'/cache/currencies.txt';