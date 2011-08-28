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
            <input type='submit' onClick=\"alert('This might take some time.\\nPlease be patient!')\"/>
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
      global $config, $display_names,$gids;
      $ips = array();
      
      if(!$player && isset($ip))
            $condition = "(strpos($line,$ip) !== false)";
      elseif(isset($player) && !$ip)
            $condition = "(strpos($line,$player) !== false)";
      elseif(isset($player) && isset($ip))
            $condition = "(strpos($line,$ip) !== false && strpos($line,$player) !== false)";

      eval("\$ok =  \"$condition)\";");

      foreach(file($config["ip_log"]) as $line)
      {
            if($ok)
            {
                  $line = explode(" ",$line);

                  if(count($line) == 3)
                  {
                        // PLAYER_ENTERED
                        if(!in_array($line[2],$display_names))
                              array_push($display_names,$line[2]);

                        if(!in_array($line[1],$ips))
                              array_push($ips,$line[1]);
                  }
                  elseif(count($line) == 5 && ($line[3] == "1" || $line[3] == "0"))
                  {
                        // PLAYER_RENAMED
                        if(strpos($line[1],"@") !== false && !in_array($line[1],$gids))
                              array_push($gids,$line[1]);
                        if(!in_array($line[4],$display_names))
                              array_push($display_names,$line[4]);
                              
                        if(!in_array($line[2],$ips))
                              array_push($ips,$line[2]);
                  }
                  else
                  {
                        // Player's name has spaces in it
                        if(strpos($line[3],"1") !== false || strpos($line[3],"0") !== false)
                        {
                              // PLAYER_RENAMED
                              if(strpos($line[1],"@") !== false && !in_array($line[1],$gids))
                                    array_push($gids,$line[1]);
                                    
                              if(!in_array($line[2],$ips))
                                    array_push($ips,$line[2]);
                                    
                              $name = "";
                              foreach((array_slice($line,4)) as $part)
                              {
                                    $name .= "$part ";
                              }
                              if(!in_array(trim($name),$display_names))
                                    array_push($display_names,trim($name));

                        }
                        else
                        {
                              // PLAYER_ENTERED
                              if(!in_array($line[1],$ips))
                                    array_push($ips,$line[1]);
                                    
                              $name = "";
                              foreach((array_slice($line,2)) as $part)
                              {
                                    $name .= "$part ";
                              }
                              if(!in_array(trim($name),$display_names))
                                     array_push($display_names,trim($name));

                        }
                  }
                  $match = true;
            }
      }
      
      if(isset($player) && !$ip)
      {
            foreach($ips as $ip)
                  search($ip,null);
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