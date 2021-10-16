<?php

namespace Micronative\EventSchema\Exceptions;

class ConsumerException extends ServiceSchemaException
{
    const FAILED_TO_CREATE_MESSAGE = "Failed to create message from json string: ";
    const NO_REGISTER_EVENTS = "No registered events for event name: %s, version: %s";
    const NO_REGISTER_SERVICES = "No registered services for event name: %s, version: %s";
    const FILTERED_EVENT_ONLY = "Only filtered events are allowed to process: ";
}
