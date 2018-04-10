<?php

if ( ! defined( 'ABSPATH' ) ) exit;

// http://php.net/manual/en/function.json-encode.php#105789
function json_encode_utf8($arr, $options = 0) {
  //convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
  array_walk_recursive($arr, function (&$item, $key) {
    if (is_string($item))
      $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
  });
  return mb_decode_numericentity(json_encode($arr, $options), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

// https://gist.github.com/NickBeeuwsaert/7568762
function json_encode_pretty($arr, $indent="    ", $characters=array(", ", ": "), $depth=0, $eol=PHP_EOL) {
  //You'd think that with the plethora of functions PHP has for array operations, they'd have one to check
  // if an array is associative or not
  // Now, I know what your saying "Oh, but, ALL arrays in PHP are associative!" Suck it, you know what I mean
  $is_assoc = (array_keys($arr) !== range(0,count($arr)-1));
  
  end($arr);
  $last = key($arr);
  $result = ($is_assoc?"{":"[").$eol; //Print whether or not the array is an object
  foreach($arr as $key=>$val){
      $result .= str_repeat($indent, $depth+1);//Indent it
      if($is_assoc) // if is assocative, print the key name
          $result .= "\"$key\"".$characters[1];
      if(is_array($val)) //If the value is an array, encode that
          $result .= json_pretty_encode($val, $indent, $characters, $depth+1, $eol);
      else if(is_bool($val)) //ensure boolean values are printed as such
          $result .= $val?"true":"false";
      else if(is_numeric($val)) //Print out numbers
          $result .= $val;
      else //Everything else is a string
          $result .= "\"".addslashes($val)."\"";
      //if this is the last element just print a newline, otherwise print $characters[0]
      $result .= (($key==$last)?"":$characters[0]).$eol;
  }
  $result .= str_repeat($indent, $depth).($is_assoc?"}":"]"); //Close the object
  return $result;
}

// https://stackoverflow.com/questions/6743554/slash-issue-with-json-encode-why-and-how-to-solve-it
function json_unescape_slashes($json_str) {
  return str_replace('\\/', '/', $json_str);
}
