<?php
namespace d52b8;

class CsvHelper
{
    private static function encode($text)
    {
        if (mb_detect_encoding($text, 'Windows-1251', true) == 'Windows-1251') {
            return iconv('cp1251', "utf-8", $text);
        }
        return $text;
        // return iconv(mb_detect_encoding($text, mb_detect_order(), true), "utf-8", $text);
    }

    private static function removeBom($text) {
        if (substr($text, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
            $text = substr($text, 3);
        }
        return $text;
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
                        $row = array_map('self::removeBom', $row);
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
