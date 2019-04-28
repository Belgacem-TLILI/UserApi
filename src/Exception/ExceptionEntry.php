<?php

namespace Belga\Exception;

/**
 * Entry to keeps one inner error of Exception.
 */
class ExceptionEntry
{
    /** Error code
     *
     * @var string
     */
    private $code;

    /** Error message
     *
     * @var string
     */
    private $message;

    /** Field name for which is error related.
     *
     * @var string
     */
    private $field;

    public function __construct($message, $code, $field)
    {
        $this->code = $code;
        $this->message = $message;
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }
}
