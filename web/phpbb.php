<?php
define('IN_PHPBB', TRUE);
$phpbb_root_path = $config["phpbb"]["path"];
$phpEx = substr(strrchr(__FILE__, '.'), 1);
// Include needed files
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
// Initialize phpBB user session
$user->session_begin();
$auth->acl($user->data);
$user->setup();
?>