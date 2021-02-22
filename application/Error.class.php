<?php

class tSysError
{
    public static function handle_exception($e, $message = '', $code = 0)
    {
        $errortype = array(E_ERROR             => "Error",
                            E_WARNING           => "Warning",
                            E_PARSE             => "Parsing Error",
                            E_NOTICE            => "Notice",
                            E_CORE_ERROR        => "Core Error",
                            E_CORE_WARNING      => "Core Warning",
                            E_COMPILE_ERROR     => "Compile Error",
                            E_COMPILE_WARNING   => "Compile Warning",
                            E_USER_ERROR        => "User Error",
                            E_USER_WARNING      => "User Warning",
                            E_USER_NOTICE       => "User Notice",
                            E_RECOVERABLE_ERROR => "Recoverable Error");

        if (is_object($e)) {
            $trace   = $e->getTrace();
            $code    = $e->getCode();
            $message = $e->getMessage();
            $trace_pos  = 0;
            $trace_pos2 = 0;
        } else {
            $trace      = debug_backtrace();
            $trace_pos  = 2;
            $trace_pos2 = 1;
        }
        
        $err = date("d.m.Y - H:i:s").":\r\n\n";
        $err .= "\tError-Code:    " .$code."\r\n";
        $err .= "\tError-Type:    " .(isset($errortype[$code]) ? $errortype[$code] : '')."\r\n";
        $err .= "\tError-Msg:     " .trim($message)."\r\n";

        // prevent logging a mistyped password
        if (strpos($message, 'Invalid credentials') === false) {
            $err .= "\tMethod:        " .self::get_trace_num($trace, $trace_pos).' '.self::get_trace_file($trace, $trace_pos2)."\r\n";
            $err .= "\tTrace:         " .self::get_trace($trace, $trace_pos2)."\r\n\r\n";
        }

        $log_path = __SITE_PATH.'/error';
        
        $logfile = realpath($log_path)."/error.log";
        if (file_exists($logfile) && filesize($logfile) > 1048576) {
            unlink($logfile);
        }
        error_log($err, 3, $logfile);
    }

    public static function get_trace($trace, $pos = 0)
    {
        $btrace = array();
        for ($i = count($trace)-1; $i > $pos; $i--) {
            $btrace[] = self::get_trace_num($trace, $i);
        }
        return implode(" -> ", $btrace);
    }
    
    public static function get_trace_num($trace, $num)
    {
        $class_name = isset($trace[$num]['class']) ?    $trace[$num]['class'] : "";
        $type_name  = isset($trace[$num]['type']) ?     $trace[$num]['type'] : "";
        $function   = isset($trace[$num]['function']) ? $trace[$num]['function'] : "";
        $args       = isset($trace[$num]['args']) ?     $trace[$num]['args'] : "";
        
        return $class_name.$type_name.$function.($args ? self::get_args($args) : "");
    }

    public static function get_trace_file($trace, $num)
    {
        $file = isset($trace[$num]['file']) ? $trace[$num]['file'] : "";
        $line = isset($trace[$num]['line']) ? $trace[$num]['line'] : "";

        $file = basename(dirname(dirname($file)))."/".basename(dirname($file))."/".basename($file);

        return "in $file Line $line";
    }
    
    public static function get_args($args)
    {
        $str_args = array();
        for ($i=0; $i<count($args); $i++) {
            if (is_string($args[$i]) && strlen($args[$i]) < 64) {
                $str_args[] = "'".$args[$i]."'";
            } elseif (is_string($args[$i]) && strlen($args[$i]) >= 64) {
                $str_args[] = "&string(".strlen($args[$i]).")";
            } elseif (is_bool($args[$i])) {
                $str_args[] = $args[$i] ? "true" : "false";
            } elseif (is_array($args[$i])) {
                $str_args[] = "&array(".count($args[$i]).")";
            } elseif (is_object($args[$i])) {
                $str_args[] = "&object";
            } else {
                $str_args[] = $args[$i];
            }
        }
        return "(".implode(", ", $str_args).")";
    }
}

function log_error($err)
{
    if (is_object($err) || is_array($err)) {
        $err = print_r($err, true);
    }

    $log_path = __SITE_PATH.'/error';
                        
    $err .= "\r\n";
    $logfile = realpath($log_path)."/error.log";
    if (file_exists($logfile) && filesize($logfile) > 1048576) {
        unlink($logfile);
    }
    error_log($err, 3, $logfile);
}

function handle_error($code, $message)
{
    tSysError::handle_exception(false, $message, $code);
}

set_error_handler('handle_error', E_ALL);
