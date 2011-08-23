<?php

$config = array();

$config["ip_log"] = "log.txt"; // path to your trimmed log.txt

$config["auth_method"] = "file";
 /* --- Options are:
        - none (make it public)
        - file (just save the username/password within $config["file"])
        - db (authenticate via mysql)
    --- */
    

// depening on which auth method you choose, you have to edit $config[your_auth_method]
    
$config["file"] = array("AI-team" => "test",
                        "password_hash_method" => "none" //this has to be either "none" or "md5"
                        );
// you can add more users by adding another entry to the array

$config["db"] = array("host" => "localhost",
                      "name" => "root",  
                      "password" => "",
                      "database" => "ip",
                      "table" => "users",
                      "user_row" => "user",
                      "password_row" => "password",
                      "password_hash_method" => "none" // this has to be either "none" or "md5"
                      );        
                              
?>