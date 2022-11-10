<?php

namespace Core;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Path;

abstract class Document
{
    const SHORT_TAG = ["meta", "link"];

    static public function load($position = "head")
    {
        $json = json_decode(file_get_contents(LOCATION_PUBLIC . "/static/load.json"), TRUE);
        $json = Arrays::getValue($json, $position, []);

        $html = "";

        foreach ($json as $line) {
            $isFolder = $line['isFolder'] ?? false;

            if ($isFolder) {
                $files = array_values(array_diff(scandir(Path::normalize(LOCATION_PUBLIC . "/" . $line['folder'])), ['.', '..']));


                foreach ($files as $file) {
                    $html .= "<{$line['tag']}";
                    foreach ($line['attributes'] as $key => $value) {
                        $value = str_replace("<PATH>", $line['path'] . "/" . $file, $value);
                        $html .= " {$key}=\"{$value}\"";
                    }

                    $html .= ">" . (self::isShortTag($line['tag']) ? "" : "</{$line['tag']}>");
                    $html .= "\n";
                }
            } else {
                $html .= "<{$line['tag']}";

                foreach ($line['attributes'] as $key => $value) {
                    $html .= " {$key}=\"{$value}\"";
                }

                $html .= ">" . (self::isShortTag($line['tag']) ? "" : "</{$line['tag']}>");
                $html .= "\n";
            }
        }

        return $html;
    }

    static private function isShortTag($tag)
    {
        return Arrays::keyExists(self::SHORT_TAG, $tag);
    }
}
