<?php
namespace d52b8;

class CsvHelper
{
    public static function isUtf($str)
    {
        return (mb_detect_encoding($str, 'UTF-8', true));
    }

    public static function isWindows($str)
    {
        return (mb_detect_encoding($str, 'Windows-1251'));
    }
    
    public static function isBom($str)
    {
        if (self::isUtf($str) && substr($str, 0,3) == pack("CCC", 0xef, 0xbb, 0xbf)) {
            return true;
        }
    }
    
    public static function removeBom($str)
    {
        if (self::isBom($str)) {
            return substr($str, 3);
        } 
        return $str;
    }

    public static function encode($str)
    {
        // if (!self::isUtf($str) && self::isWindows($str)) {
        if (!self::isUtf($str)) {
            return iconv('cp1251', "utf-8//IGNORE", $str);
        }
        return $str;
    }

    public static function test()
    {
        echo "Hello, World!";
    }

    public static function read($path, $encode = true) 
    {
        try {
            $file = new \SplFileObject($path);
            $file->setFlags(
                \SplFileObject::READ_CSV |
                \SplFileObject::SKIP_EMPTY |
                \SplFileObject::READ_AHEAD
            );
            $file->setCsvControl(';',"\"");
            $header = false;
            while (!$file->eof()) {
                $row = $file->fgetcsv();
                if ($encode) {
                    $row = array_map('self::encode', $row);
                }
                $row = array_map('trim', $row);
                if ($row) {
                    if (!$header) {
                        $row = array_map('self::removeBOM', $row);
                        $row = array_map('strtolower', $row);
                        $header = $row;
                        continue;
                    }
                    if (count($header) == count($row)) {
                        $row = array_combine($header, $row);
                        yield $row;
                    }
                }
            }
        } catch (Exception $e) {
            
        }
    }
}
