<?php
namespace d52b8;

class CsvHelper
{
    public static function isUtf($str)
    {
        return (mb_detect_encoding($str, 'UTF-8', true));
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

    private static function encode($str)
    {
        if (self::isUtf($str)) {
            return $str;
        }
        return iconv('cp1251', "utf-8", $str); 
    }

    public static function test()
    {
        echo "Hello, World!";
    }

    public static function read($path) 
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
                $row = $file->current();
                $row = array_map('self::encode', $row);
                $row = array_map('trim', $row);
                $file->next();
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

