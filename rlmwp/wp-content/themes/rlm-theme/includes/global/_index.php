<?php

if (!defined('ABSPATH'))
{
   exit;
}

require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'AutoLoader.php']);
require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'setup.php']);
require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'handle-assets.php']);
require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'PHPMailer.php']);
require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'svg-support.php']);
require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'dns-prefetch.php']);
require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'template-tags']);
require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'jetpack']);
