<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Hopeter1018\FileOperation;

/**
 * Description of Path
 *
 * @version $id$
 * @author peter.ho
 */
final class Path
{

    /**
     * Return the path 
     * - without starting and ending "/"
     * 
     * @param string $path
     * @return string
     */
    public static function filePath($path)
    {
        return trim(static::pathSlashes($path), '/');
    }

    /**
     * Return the path 
     * - with "/" at the end.
     * - without starting "/"
     * 
     * @param string $path
     * @return string
     */
    public static function dirPath($path)
    {
        return trim(static::pathSlashes($path), '/') . '/';
    }

    /**
     * Return all path seperator "\" to "/"
     * 
     * @param string $path
     * @return string
     */
    public static function pathSlashes($path)
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * Return the concated path.
     * <ul>
     * <li>with "/" at the end.</li>
     * <li>without starting "/"</li>
     * </ul>
     * <b>*</b> Can pass unlimited parameters<br />
     * <b>**</b> empty parameter will be ignored<br />
     * 
     * @param string $paths,...
     * @return string
     */
    public static function concatPath()
    {
        $parts = func_get_args();
        array_walk($parts, function(&$val) {
            if (is_array($val)) {
                $val = call_user_func_array(array(__CLASS__, 'concatPath'), $val);
            } else {
                $val = $val === '' ? '' : Path::dirPath($val);
            }
        });
        return implode("", $parts);
    }

    /**
     * Return the path of $dest relative from $relativeTo
     * 
     * @param string $dest
     * @param string $relativeTo
     * @return string
     */
    public static function relativeTo($dest, $relativeTo)
    {
        return str_replace(
            static::pathSlashes($relativeTo), '', static::pathSlashes($dest)
        );
    }

    public static function depthRelativeTo($dest, $relativeTo)
    {
        return substr_count(static::relativeTo($dest, $relativeTo), '/');
    }

    public static function rootConcatPath()
    {
        return (DIRECTORY_SEPARATOR === '/' ? "/" : '') . call_user_func_array(array(__CLASS__, 'concatPath'), func_get_args());
    }

}
