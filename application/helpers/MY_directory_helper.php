<?php
/**
 * Created by PhpStorm.
 * User: bernardtrevisan_imac
 * Date: 23/12/2016
 * Time: 12:28
 */
function directory_map($source_dir, $directory_depth = 0, $hidden = FALSE)
{
    $filedata = array();
    if ($fp = @opendir($source_dir))
    {
        $new_depth	= $directory_depth - 1;
        $source_dir	= rtrim($source_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        while (FALSE !== ($file = readdir($fp)))
        {
            // Remove '.', '..', and hidden files [optional]
            if ($file === '.' OR $file === '..' OR ($hidden === FALSE && $file[0] === '.'))
            {
                continue;
            }

            is_dir($source_dir.$file) && $file .= DIRECTORY_SEPARATOR;

            if (($directory_depth < 1 OR $new_depth > 0) && is_dir($source_dir.$file))
            {
                $filedata[$file] = directory_map($source_dir.$file, $new_depth, $hidden);
            }
            else
            {
                $filedata[] = $file;
            }
        }

        closedir($fp);
    }

    return $filedata;
}
