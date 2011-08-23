<?php
session_start();

require_once("auth.php");
require_once("functions.php");


$username = (isset($_POST['username']))?$_POST['username']: null; 
$password = (isset($_POST['password']))?$_POST['password']: null;

html_header();

if(isset($_SESSION["logged_in"]) && $_SESSION["logged_in"] == true)
{
      $result = true;
} elseif($config["auth_method"] == "none") {
      $result = true;
} elseif((!$username || !$password)) {

      if(!$username && !$password)
      {     
            // the user didn't try to log in yet, so let's give him a chance to do so
            html_form();
            html_footer();
      }
      else
      {
            // missing password/username
            print_error_message("Missing password/username.");
            html_form($username, $password);
            html_footer();
      }
} else { 
      $result = authenticate($username,$password);
}


if(!$result)
{
      print_error_message("Invalid password/username.");
      html_form();
      html_footer();      
} 
      
$_SESSION["logged_in"] = true;

$ip = (isset($_POST['ip']))?$_POST['ip']: null; 

if(!$ip)
{
      ip_form();
      html_footer();
}

if(!validate_ip($ip))
{
      print_error_message("Invalid IP.");
      ip_form();
      html_footer();      
} 

$display_names = array();
$gids = array();

$result = search($ip);

if(!$result)
{
      print_error_message("No matching entries found.");
      ip_form();
      html_footer();      
} 

format_output($display_names,$gids);
html_footer(); 
     

   




?>