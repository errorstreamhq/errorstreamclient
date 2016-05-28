<?php

namespace ErrorStream\ErrorStreamClient;

class ErrorStreamReport
{
    public $error_group;
    public $line_number;
    public $file_name;
    public $message;
    public $stack_trace;
    public $severity;
}