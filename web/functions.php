<?php

function html_header()
{
      echo "<html>
            <head>
            <title>Armagetron Advanced IP Lookup</title>
            </head>
            <body><br /><br />
            <h1 align='center'>Armagetron Advanced IP Lookup</h1><br />";
}

function html_footer()
{
      echo "</body></html>";
      die();
}

function print_error_message($msg)
{
      echo "<div align='center'><b>$msg</b></div>";
}

function html_form($username=null, $password=null)
{
      echo "<div align='center'>
            <form action='index.php' method='post'>
            Username: <input type='text' name='username' value='$username'/><br />
            Password: <input type='password' name='password' value='$password'/><br />
            <input type='submit' value='Login' />
            </form></div>";
}

function ip_form()
{
      echo "<div align='center'>
            <form action='index.php' method='post'>
            IP:<br />
            <input type='text' name='ip' /><br />
            Player:<br />
            <input type='text' name='player' /><br />
            <input type='submit' onclick='alert('This might take some time.\nPlease be patient!') /> 
            </form></div>";
}

function validate_ip($ip)
{
      if(preg_match("^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){1,3}^", $ip))
            return true;
      else
            return false;
}

function search($ip=null,$player=null)  
{
      global $config;
      global $display_names;
      global $gids;
      $last_ips = array();
      
      if(!$ip && !$player)
            return false;
            
      if(!$player && isset($ip))
      {
            // search for IP only
            foreach(file($config["ip_log"]) as $line)
            {
                  if(strpos($line,$ip) !== false) 
                  {
                        $line = explode(" ",$line); 
                        
                        if(count($line) == 3)
                        {
                              // PLAYER_ENTERED
                              if(!in_array($line[2],$display_names))
                                    array_push($display_names,$line[2]);
                        }
                        elseif(count($line) == 5)
                        {
                              // PLAYER_RENAMED
                              if(strpos($line[1],"@") !== false && !in_array($line[1],$gids))
                                    array_push($gids,$line[1]);
                              if(!in_array($line[4],$display_names))     
                                    array_push($display_names,$line[4]);    
                        }
                        $match = true;
                  }      
            }      
      }
      
      if(!$ip && isset($player))
      {
            // search for player name only
            foreach(file($config["ip_log"]) as $line)
            {
                  if(strpos($line,$player) !== false) 
                  {
                        $line = explode(" ",$line); 
                        
                        if(count($line) == 3)
                        {
                              // PLAYER_ENTERED
                              if(!in_array($line[2],$display_names))
                                    array_push($display_names,$line[2]);
                                    
                              array_push($last_ips,$line[1]);  // remember the last known IP for later use
                               
                        }
                        elseif(count($line) == 5)
                        {
                              // PLAYER_RENAMED
                              if(strpos($line[1],"@") !== false && !in_array($line[1],$gids))
                                    array_push($gids,$line[1]);
                              if(!in_array($line[4],$display_names))     
                                    array_push($display_names,$line[4]);
                                    
                              array_push($last_ips,$line[2]); // remember the last known IP for later use 
                        }
                        $match = true;
                  }      
            } 
            
            if(empty($last_ips)) 
                  return false;

            // search for entries matching the player's IP
            foreach(file($config["ip_log"]) as $line)
            {
                  if(array_walk($last_ips,create_function('$ip' , 'return (strpos($line,$ip) !== false)?true:false;'))) 
                  {
                        $line = explode(" ",$line); 
                        
                        if(count($line) == 3)
                        {
                              // PLAYER_ENTERED
                              if(!in_array($line[2],$display_names))
                                    array_push($display_names,$line[2]);
                        }
                        elseif(count($line) == 5)
                        {
                              // PLAYER_RENAMED
                              if(strpos($line[1],"@") !== false && !in_array($line[1],$gids))
                                    array_push($gids,$line[1]);
                              if(!in_array($line[4],$display_names))     
                                    array_push($display_names,$line[4]);   
                        }
                        $match = true;
                  }      
            } 
                            
      }
      
      if(isset($player) && isset($ip))
      {
            // search for entries matching both, the IP and the player's name
            foreach(file($config["ip_log"]) as $line)
            {
                  if(strpos($line,$ip) !== false && strpos($line,$player) !== false) 
                  {
                        $line = explode(" ",$line); 
                        
                        if(count($line) == 3)
                        {
                              // PLAYER_ENTERED
                              if(!in_array($line[2],$display_names))
                                    array_push($display_names,$line[2]);
                        }
                        elseif(count($line) == 5)
                        {
                              // PLAYER_RENAMED
                              if(strpos($line[1],"@") !== false && !in_array($line[1],$gids))
                                    array_push($gids,$line[1]);
                              if(!in_array($line[4],$display_names))     
                                    array_push($display_names,$line[4]);   
                        }
                        $match = true;
                  }      
            }      
      }      
      
      if($match != true)
            return false;
      else
            return true;      
}


function format_output($names,$gids)
{
      if(count($gids) == 0)
            array_push($gids,"-");
            
     echo "<div style='margin-left: 45%;'>"
         ."<ul><li>Display names:
            <ul>";
      foreach($names as $name)
            echo "<li>$name</li>\n";
      echo "</ul></li>"
          ."<li>GIDs:
            <ul>";
      foreach($gids as $gid)
            echo "<li>$gid</li>";
      echo "</ul></li></ul>" 
          ."</div>";
          
      echo "<br /><br/><div align='center'>"   
          ."<a href='index.php'>Start new search<a/>"
          ."</div>";     
}
?>