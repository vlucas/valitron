<?php
namespace Valitron;

/**
 * Validation Class
 *
 * Validates input against certian criteria
 *
 * @package Valitron
 * @author Vance Lucas <vance@vancelucas.com>
 * @link http://www.vancelucas.com/
 */
class Validator
{
    protected $_fields = array();
    protected $_errors = array();
    protected $_validations = array();
    protected static $_rules = array();


    /**
     *  Setup validation
     */
    public function __construct($data, $fields = array())
    {
        // Allows filtering of used input fields against optional second array of field names allowed
        // This is useful for limiting raw $_POST or $_GET data to only known fields
        foreach($data as $field => $value) {
            if(empty($fields) || (!empty($fields) && in_array($field, $fields))) {
                $this->_fields[$field] = $value;
            }
        }
    }


    /**
     * Register new validation rule callback
     */
    public static function addRule($name, $callback)
    {
        if(!is_callable($callback)) {
            throw new \InvalidArgumentException("Second argument must be a valid callback. Given argument was not callable.");
        }

        static::$_rules[$name] = $callback;
    }


    /**
     * Convenience method to add validation rules
     */
    public function rule($rule, $fields, $message = null)
    {
        if(!isset(static::$_rules[$rule])) {
            throw new \InvalidArgumentException("Rule '" . $rule . "' has not been registered with " . __CLASS__ . "::addRule().");
        }

        $this->_validations[] = array(
            'rule' => $rule,
            'fields' => (array) $fields,
            'message' => $message
        );
        return $this;
    }


    /**
     * Convenience method to add validation rules
     */
    public function __call($rule, array $args)
    {
        if(!isset(static::$_rules[$rule])) {
            throw new \InvalidArgumentException("Method '" . $rule . "' does not exist, or rule '" . $rule . "' has not been registered with " . __CLASS__ . "::addRule().");
        }

        array_unshift($args, $rule);
        call_user_func_array(array($this, 'rule'), $args);
        return $this;
    }

    /**
     *  Required field validator
     */
    public function validateRequired($field, $value, array $params = array())
    {
        if(is_null($value)) {
            return false;
        } elseif(is_string($value) and trim($value) === '') {
            return false;
        }
        return true;
    }

    /**
     * Validate that an attribute is a valid IP address
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    public static function validateIp($field, $value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validate that an attribute is a valid e-mail address
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    public static function validateEmail($field, $value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate that an attribute is a valid URL by syntax
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    public static function validateUrl($field, $value)
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Validate that an attribute is an active URL by verifying DNS record
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    public static function validateActiveUrl($field, $value)
    {
        $url = str_replace(array('http://', 'https://', 'ftp://'), '', strtolower($value));

        return checkdnsrr($url);
    }

    /**
     * Validate that an attribute contains only alphabetic characters
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    public static function validateAlpha($field, $value)
    {
        return preg_match('/^([a-z])+$/i', $value);
    }

    /**
     * Validate that an attribute contains only alpha-numeric characters.
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    public static function validateAlphaNum($field, $value)
    {
        return preg_match('/^([a-z0-9])+$/i', $value);
    }

    /**
     * Validate that an attribute contains only alpha-numeric characters, dashes, and underscores.
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    public static function validateAlphaDash($field, $value)
    {
        return preg_match('/^([-a-z0-9_-])+$/i', $value);
    }

    /**
     * Validate that an attribute passes a regular expression check.
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    public static function validateRegex($field, $value, $params)
    {
        return preg_match($params[0], $value);
    }

    /**
     * Validate that an attribute is a valid date.
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    public static function validateDate($field, $value)
    {
        return strtotime($value) !== false;
    }

    /**
     * Validate that an attribute matches a date format.
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    public static function validateDateFormat($field, $value, $params)
    {
        $parsed = date_parse_from_format($params[0], $value);

        return $parsed['error_count'] === 0;
    }

    /**
     * Validate the date is before a given date.
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    public static function validateBefore($field, $value, $params)
    {
        return strtotime($value) < strtotime($params[0]);
    }

    /**
     * Validate the date is after a given date.
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    public static function validateAfter($field, $value, $params)
    {
        return strtotime($value) > strtotime($params[0]);
    }

    /**
     *  Get array of fields and data
     */
    public function data($field = null)
    {
        return $this->_fields;
    }

    /**
     *  Get array of error messages
     */
    public function errors($field = null)
    {
        if($field !== null) {
            return isset($this->_errors[$field]) ? $this->_errors[$field] : false;
        }
        return $this->_errors;
    }

    /**
     *  Add an error to error messages array
     */
    public function error($field, $msg)
    {
        // Add to error array
        $this->_errors[$field][] = sprintf($msg, $field);
    }

    /**
     * Run validations and return boolean result
     *
     * @return boolean
     */
    public function validate()
    {
        foreach($this->_validations as $v) {
            foreach($v['fields'] as $field) {
                $value = isset($this->_fields[$field]) ? $this->_fields[$field] : null;
                $result = call_user_func(static::$_rules[$v['rule']], $field, $value);
                if(!$result) {
                    $this->error($field, $v['message']);
                }
            }
        }
        return count($this->errors()) === 0;
    }
}

// Register default validations here so they can be overridden by user after include
$class = __NAMESPACE__ . '\Validator';
$class::addRule('required', array($class, 'validateRequired'));
$class::addRule('email', array($class, 'validateEmail'));
$class::addRule('url', array($class, 'validateUrl'));

