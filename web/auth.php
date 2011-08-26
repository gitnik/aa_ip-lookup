<?php
require("config.php");
require_once("phpbb.php");
require("config.php"); //include config again, because phpbb overwrote $config

function authenticate($username=null,$password=null)
{
      global $config;
      global $user;
      
      switch($config["auth_method"])
      {
            case "file":
                  if($config["file"]["password_hash_method"] == "md5")
                  {
                        if((@$config["file"][$username] == md5($password)))
                              return true;
                        else
                              return false;
                  }
                  elseif($config["file"]["password_hash_method"] == "none")
                  {
                        if((@$config["file"][$username] == $password))
                              return true;
                        else
                              return false;      
                  }
                  else die("you have set a non valid password hash method (file)");
                  break;
            case "db":
                  $link = @mysql_connect($config["db"]["host"], $config["db"]["name"], $config["db"]["password"]);
                  
                  if(!$link)
                        die("couldn't establish a connection. reason given by mysql database:<br>". mysql_error());
                        
                  mysql_select_db($config["db"]["database"], $link);
                  
                  $sql = 'SELECT ' . $config["db"]["password_row"] . ' FROM ' . $config["db"]["table"] . 
                         ' WHERE ' . $config["db"]["user_row"] . ' = "' . $username . '"'; 
                         
                  $query = mysql_query($sql, $link) OR die(mysql_error());
                  
                  $db_password = mysql_fetch_row($query); $db_password = $db_password[0];
                  if(!$db_password)
                        return false;
                        
                  if($config["db"]["password_hash_method"] == "md5")
                  {
                        if($db_password == md5($password))
                              return true;
                        else
                              return false;
                  }
                  elseif($config["db"]["password_hash_method"] == "none")
                  {
                        if($db_password == $password)
                              return true;
                        else
                              return false;      
                  }
                  else die("you have set a non valid password hash method (db)");
                  break;
            case "phpbb":
                  if(empty($config["phpbb"]["users"]) && empty($config["phpbb"]["groups"]))
                        die("do you really want to give access to nobody? (phpbb)");
                  else
                  {
                        if(in_array($user->data["username"],$config["phpbb"]["users"]))
                              return true;
                        if(in_array($user->data["group_id"],$config["phpbb"]["groups"]) || in_array($user->data["user_type"],$config["phpbb"]["groups"]))
                              return true;

                        return false;
                  }
                  break;
            case "none":
                  return true;
                  break;                                  
            default:
                  die("you have set a non valid authentication method");   
      }
      
}

?>