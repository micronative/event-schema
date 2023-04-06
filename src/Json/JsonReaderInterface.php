<?php

namespace Micronative\EventSchema\Json;

interface JsonReaderInterface
{
    /**
     * @param string|null $file
     * @return string
     */
    public static function read(string $file);

    /**
     * @param string|null $json
     * @param bool $assoc
     * @return array|mixed
     */
    public static function decode(string $json, bool $assoc);

    /**
     * @param null $content
     * @return false|string
     */
    public static function encode($content);
}
