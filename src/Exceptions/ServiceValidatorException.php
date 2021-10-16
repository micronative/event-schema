<?php

namespace Micronative\EventSchema\Exceptions;

class ServiceValidatorException extends ServiceSchemaException
{
    const INVALID_JSON_STRING = "Event->toJson is invalid Json string.";
    const INVALIDATED_EVENT = "Event is not validated by Service Schema. Error: ";
}
