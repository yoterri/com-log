<?php

namespace Com\Component\Log;

use Zend\Log\Logger as zLogger;

class Logger extends zLogger
{

    protected $enabled = true;


    function setEnabled($value)
    {
        $this->enabled = (bool)$value;
        return $this;
    }


    function getEnabled()
    {
        return $this->enabled;
    }


    /**
     * @param string $priority
     * @param string $message
     * @param array|Traversable $extra
     * @param string $ref
     * @return Logger
     */
    public function log($priority, $message, $extra = [], $ref = null)
    {
        $force = true;

        if(!$this->enabled)
        {
            if((self::DEBUG == $priority))
            {
                return $this;
            }
        }

        if($extra)
        {
            $encoded = $this->_normalize($extra);
            $extra = ['debug_value' => $encoded];
        }
        else
        {
            $extra = [];
        }

        $backtrace = debug_backtrace();

        #
        $caller = $backtrace[1];
        if (isset($caller['file'])) {
            $file = $caller['file'];
            $file = str_replace(dirname(dirname(APPLICATION_PATH)), '', $file);

            if (isset($caller['line']))
                $file .= " ({$caller['line']})";

            $extra['caller'] = $file;
        }

        $fn = isset($backtrace[2]) ? $backtrace[2] : [];
        if(isset($fn['function'])) {
            $extra['function'] = $fn['function'];
        }

        if(is_object($message))
        {
            if($message instanceof \exception)
            {
                $extra['exception_message'] = $message->getMessage();
                $extra['exception_line'] = $message->getLine();
                $extra['exception_file'] = $message->getFile();
                $extra['exception_code'] = $message->getCode();
                $extra['exception_message'] = $message->getMessage();

                $extra['exception'] = $message->getTraceAsString();
                $message = "Exception: {$message->getMessage()}";
            }
            else
            {
                if(method_exists($message, 'toArray()'))
                {
                    $extra[] = $message->toArray();
                    $message = 'Class: ' . get_class($message);
                }
                elseif(method_exists($message, '__toString'))
                {
                    $extra[] = (string)$message;
                    $message = 'Class: ' . get_class($message);
                }
                else
                {
                    $extra[] = get_class($message);
                    $message = 'Class: ' . get_class($message);
                }
            }
        }

        if(strlen($message) > 250)
        {
            $message = substr($message, 0, 250-1);
        }

        if(!empty($ref))
        {
            $extra['_ref_'] = $ref;
        }

        return parent::log($priority, $message, $extra);
    }


    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function emerg($message, $extra = [], $ref = null)
    {
        return $this->log(self::EMERG, $message, $extra, $ref);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function alert($message, $extra = [], $ref = null)
    {
        return $this->log(self::ALERT, $message, $extra, $ref);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function crit($message, $extra = [], $ref = null)
    {
        return $this->log(self::CRIT, $message, $extra, $ref);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function err($message, $extra = [], $ref = null)
    {
        return $this->log(self::ERR, $message, $extra, $ref);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function warn($message, $extra = [], $ref = null)
    {
        return $this->log(self::WARN, $message, $extra, $ref);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function notice($message, $extra = [], $ref = null)
    {
        return $this->log(self::NOTICE, $message, $extra, $ref);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function info($message, $extra = [], $ref = null)
    {
        return $this->log(self::INFO, $message, $extra, $ref);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return Logger
     */
    public function debug($message, $extra = [], $ref = null)
    {
        return $this->log(self::DEBUG, $message, $extra, $ref);
    }



    /**
     * @param string $message
     * @param array|Traversable $extra
     * @param string $ref
     * @return Logger
     */
    public function error($message, $extra = [], $ref = null)
    {
        return $this->log(self::ERR, $message, $extra, $ref);
    }


    /**
     * @param string $message
     * @param array|Traversable $extra
     * @param string $ref
     * @return Logger
     */
    public function warning($message, $extra = [], $ref = null)
    {
        return $this->log(self::WARN, $message, $extra, $ref);
    }



    /**
     * Normalize all non-scalar data types (except null) in a string value
     *
     * @param mixed $value
     * @return mixed
     */
    protected function _normalize($value)
    {
        if (is_scalar($value) || null === $value) {
            return $value;
        }

        // better readable JSON
        static $jsonFlags;
        if ($jsonFlags === null) {
            $jsonFlags = 0;
            $jsonFlags |= defined('JSON_UNESCAPED_SLASHES') ? JSON_UNESCAPED_SLASHES : 0;
            $jsonFlags |= defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0;
            #$jsonFlags |= defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0;
            
        }

        // Error suppression is used in several of these cases as a fix for each of
        // #5383 and #4616. Without it, #4616 fails whenever recursion occurs during
        // json_encode() operations; usage of a dedicated error handler callback
        // causes #5383 to fail when the Logger is being used as an error handler.
        // The only viable solution here is error suppression, ugly as it may be.
        if ($value instanceof \DateTime) {
            $value = $value->format($this->getDateTimeFormat());
        } elseif ($value instanceof \Traversable) {
            $value = @json_encode(iterator_to_array($value), $jsonFlags);
        } elseif (is_array($value)) {
            $value = @json_encode($value, $jsonFlags);
        } elseif (is_object($value) && ! method_exists($value, '__toString')) {
            $value = sprintf('object(%s) %s', get_class($value), @json_encode($value));
        } elseif (is_resource($value)) {
            $value = sprintf('resource(%s)', get_resource_type($value));
        } elseif (! is_object($value)) {
            $value = gettype($value);
        }

        return (string) $value;
    }
}
