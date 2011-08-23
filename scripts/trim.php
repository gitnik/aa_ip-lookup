<?php
ob_start();

// --- needs to be modified ---
$sources = "/path/to/ladderlog.txt";
$target = "/path/to/trimmed/log.txt";

// --- don't change the rest --- 
$needle = array("PLAYER_ENTERED", "PLAYER_RENAMED");


foreach($sources as $source)
{
      foreach(file($source) as $haystack)
      {
            if((strpos($haystack,$needle[0]) !== false) || (strpos($haystack,$needle[1]) !== false))
                  echo trim(str_replace($needle,"",$haystack));
      }
}


$output = ob_get_contents(); 
file_put_contents($target,$output);

ob_end_clean();
?>