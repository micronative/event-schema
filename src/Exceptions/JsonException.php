<?php

namespace Micronative\EventSchema\Exceptions;

class JsonException extends ServiceSchemaException
{
    const INVALID_JSON_FILE = "Provided file is not a valid json file: ";
    const MISSING_JSON_FILE = "Missing json file";
    const MISSING_JSON_CONTENT = "Content is empty, please provide json content";
    const INVALID_JSON_CONTENT = "Provided string is not valid json: ";
}
