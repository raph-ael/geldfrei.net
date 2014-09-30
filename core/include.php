<?php 

require_once '../core/config.php';
require_once DIR_CORE.'Model.php';
require_once DIR_CORE.'View.php';
require_once DIR_CORE.'CoreController.php';
require_once DIR_CORE.'Controller.php';

require_once DIR_CORE.'Session.php';

// start session
S::init();

require_once '../lib/minify/JSMin.php';
require_once '../lib/minify/CssMin.php';
require_once DIR_CORE.'functions.php';
require_once DIR_CORE.'tools.php';
require_once DIR_CORE.'boot.php';

require_once DIR_CORE.'postboot.php';