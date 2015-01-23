<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Hopeter1018\FileOperation;

/**
 * FileTransaction class that support nested transaction level.<br />
 * 
 * @todo Cleanup when shutdown
 * @version $id$
 * @author peter.ho
 */
final class FileTransaction
{

    /**
     * The current transaction level
     * @var int 
     */
    private static $depthIndex = 0;
    /**
     * The transaction file mapping of Array(Level => Array(Tmp => Destination))
     * @var array 
     */
    private static $fileNameTmpMapping = array();

    /**
     * Begin the file transaction
     * 
     * @throws \Exception
     */
    public static function begin()
    {
        ++ static::$depthIndex;
        if (! isset(static::$fileNameTmpMapping[ static::$depthIndex ])) {
            static::$fileNameTmpMapping[ static::$depthIndex ] = array();
        } else {
            throw new \Exception("depthIndex is wrong");
        }
    }

    /**
     * Commit the file transaction<br />
     * Copy all written temp file ONLY from the same transaction level
     * 
     * @throws \Exception
     */
    public static function commit()
    {
        if (static::$depthIndex > 0) {
            foreach (static::$fileNameTmpMapping[ static::$depthIndex ] as $fileTmp => $filePath) {
                copy($fileTmp, $filePath);
            }
            unset(static::$fileNameTmpMapping[ static::$depthIndex ]);
            -- static::$depthIndex;
        } else {
            throw new \Exception("depthIndex is wrong");
        }
    }

    /**
     * Rollback the file transaction<br />
     * Remove all written temp file from the same transaction level
     * 
     * @throws \Exception
     */
    public static function rollback()
    {
        if (static::$depthIndex > 0) {
            unset(static::$fileNameTmpMapping[ static::$depthIndex ]);
            -- static::$depthIndex;
        } else {
            throw new \Exception("depthIndex is wrong");
        }
    }

    /**
     * Add new temp file into current transaction level<br />
     * 
     * @link http://php.net/manual/en/function.file-put-contents.php
     * @param type $filename
     * @param type $data
     * @param type $flags
     * @param type $context
     * @throws \Exception
     */
    public static function filePutContent($filename, $data, $flags = 0, $context = null)
    {
        if (! isset(static::$fileNameTmpMapping[ static::$depthIndex ])) {
            throw new \Exception("Not yet started transaction");
        }
        $tmpName = tempnam(\Zms5Library\Framework\SystemPath::storagePath("file-transaction"), "");
        if ($tmpName === false) {
            throw new \Exception("Failed to create temp file under: {" . APP_SYSTEM_STORAGE . "}");
        }
        static::$fileNameTmpMapping[ static::$depthIndex ][$tmpName] = $filename;
        file_put_contents($filename, $data, $flags, $context);
    }

}
