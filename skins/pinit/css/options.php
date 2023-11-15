<?php

require_once dirname(__FILE__) . '/../../../conf/_config.php';
var_dump("333");

require $inc_path . '_main.inc.php';

echo 'a {" ' . $Skin->get_setting('link_color') . ' }';
