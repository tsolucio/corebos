<?php

//Finds recursively if there are php scripts with trailing space after php closing tag.

if ( ! function_exists('glob_recursive'))
{
    // Does not support flag GLOB_BRACE
   
    function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
       
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }
       
        return $files;
    }
}

foreach (glob_recursive("*.php") as $file){
  if (preg_match( "/\\?".">\\s\\s+\\Z/m", file_get_contents($file)))
    echo("$file\n");
}
?>