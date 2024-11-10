<?php

if (!defined('ABSPATH'))
{
   echo 'Inicie WordPress';
   exit;
}

define('LOGIN_ENDPOINT', 'login');

$run_once_file_path = implode(DIRECTORY_SEPARATOR, [__DIR__, 'includes', 'run-once.php']);

if (file_exists($run_once_file_path))
{
   require $run_once_file_path;
}

require implode(DIRECTORY_SEPARATOR, [__DIR__, 'includes', 'global', 'required-plugins.php']);
require implode(DIRECTORY_SEPARATOR, [__DIR__, 'includes', 'global', 'required-pages.php']);
require implode(DIRECTORY_SEPARATOR, [__DIR__, 'includes', 'utils', '_index.php']);
require implode(DIRECTORY_SEPARATOR, [__DIR__, 'includes', 'global', '_index.php']);
require implode(DIRECTORY_SEPARATOR, [__DIR__, 'includes', 'hooks', '_index.php']);

require implode(DIRECTORY_SEPARATOR, [__DIR__, 'modules', 'login', '_index.php']);

RLM__Utils::load_class('AutoLoader');

$loader = new RLM_Theme_AutoLoader();
$loader->add_namespace('RLM_Theme', get_theme_file_path('includes/entities'));
$loader->add_namespace('RLM_Theme', get_theme_file_path('includes/services'));

// require implode(DIRECTORY_SEPARATOR, [__DIR__, 'includes', 'generic', 'Activity_Log.php']);
require implode(DIRECTORY_SEPARATOR, [__DIR__, 'includes', 'generic', 'Head_Hooks.php']);
