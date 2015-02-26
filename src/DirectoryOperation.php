<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Hopeter1018\FileOperation;

/**
 * Description of DirectoryOperation
 *
 * @version $id$
 * @author peter.ho
 */
final class DirectoryOperation
{

    /**
     * Check if a directory is writable by adding and temp file into it.
     * @param string $dest
     * @return boolean
     */
    public static function isDirWritable($dest)
    {
        $result = false;
        if (is_dir($dest)) {
            $tempFile = tempnam($dest, 'hkc-dummy-write-test');
            file_put_contents($tempFile, 'hkc-dummy-write-test');
            if (file_get_contents($tempFile) == 'hkc-dummy-write-test') {
                $result = true;
                unlink($tempFile);
            }
        }
        return $result;
    }

    /**
     * 
     * @param string $src
     * @param string $dest
     * @param boolean $forceCopy
     * @return boolean
     */
    public static function copy($src, $dest, $forceCopy = false)
    {
        $result = false;
//        \Hopeter1018\Helper\HttpResponse::addMessageUat($src, 'src');
//        \Hopeter1018\Helper\HttpResponse::addMessageUat($dest, 'dest');
        if (is_link($src)) {    // Check for symlinks
            \Hopeter1018\Helper\HttpResponse::addMessageUat('is_link');
            $result = symlink(readlink($src), $dest);
        } else if (is_file($src)) {    // Simple copy for a file
            $result = copy($src, $dest);
        } else {
            !is_dir($dest) and mkdir($dest, 0755, true);    // Make destination directory
            \Hopeter1018\Helper\HttpResponse::addMessageUat('d2', '$dest');
            // Loop through the folder
            $dir = dir($src);
            while (false !== $entry = $dir->read()) {
                // Skip pointers
                if ($entry == '.' || $entry == '..') {
                    continue;
                }

                // Deep copy directories
                static::copy("$src/$entry", "$dest/$entry");
            }

            // Clean up
            $dir->close();
            $result = true;
        }
        return $result;
    }

}
