<?php

namespace Micronative\EventSchema\Exceptions;

class ValidatorException extends ServiceSchemaException
{
    const INVALID_JSON = "Event->toJson is invalid Json string.";
    const INVALID_SCHEMA = "Invalid event schema provided.";
    const INVALIDATED_EVENT = "Event is not validated. Error: ";
}
