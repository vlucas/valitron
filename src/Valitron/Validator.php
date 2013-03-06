<?php
namespace Valitron;

/**
 * Validation Class
 *
 * Validates input against certain criteria
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

    protected static $_lang;
    protected static $_langDir;
    protected static $_rules = array();
    protected static $_ruleMessages = array();

    const ERROR_DEFAULT = 'Invalid';

    protected $validUrlPrefixes = array('http://', 'https://', 'ftp://');

    /**
     *  Setup validation
     */
    public function __construct($data, $fields = array(), $lang = 'en', $langDir = null)
    {
        // Allows filtering of used input fields against optional second array of field names allowed
        // This is useful for limiting raw $_POST or $_GET data to only known fields
        foreach ($data as $field => $value) {
            if (empty($fields) || (!empty($fields) && in_array($field, $fields))) {
                $this->_fields[$field] = $value;
            }
        }

        // Only load language files if language or directory has changed
        if ($lang !== static::$_lang || $langDir !== static::$_langDir) {
            // Set language directory for loading language files
            if ($langDir === null) {
                $langDir = dirname(dirname(__DIR__)) . '/lang';
            }
            static::langDir($langDir);

            // Set language for error messages
            static::lang($lang);
        }
    }

    /**
     * Get/set language to use for validation messages
     */
    public static function lang($lang = null)
    {
        if ($lang !== null) {
            static::$_lang = $lang;

            // Load language file in directory
            $langDir = static::langDir();
            static::$_ruleMessages = require rtrim($langDir, '/') . '/' . $lang . '.php';
        }
        return static::$_lang;
    }

    /**
     * Get/set language file path
     */
    public static function langDir($dir = null)
    {
        if ($dir !== null) {
            static::$_langDir = $dir;
        }
        return static::$_langDir;
    }

    /**
     *  Required field validator
     */
    protected function validateRequired($field, $value)
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) and trim($value) === '') {
            return false;
        }
        return true;
    }

    /**
     * Validate that two values match
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return void
     */
    protected function validateEquals($field, $value, array $params)
    {
        $field2 = $params[0];
        return isset($this->_fields[$field2]) && $value == $this->_fields[$field2];
    }

    /**
     * Validate that a field is different from another field
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    protected function validateDifferent($field, $value, array $params)
    {
        $field2 = $params[0];
        return isset($this->_fields[$field2]) && $value != $this->_fields[$field2];
    }

    /**
     * Validate that a field was "accepted" (based on PHP's string evaluation rules)
     *
     * This validation rule implies the field is "required"
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    protected function validateAccepted($field, $value)
    {
        $acceptable = array('yes', 'on', 1, true);
        return $this->validateRequired($field, $value) && in_array($value, $acceptable, true);
    }

    /**
     * Validate that a field is numeric
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    protected function validateNumeric($field, $value)
    {
        return is_numeric($value);
    }

    /**
     * Validate that a field is an integer
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    protected function validateInteger($field, $value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate the length of a string
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    protected function validateLength($field, $value, $params)
    {
        $length = $this->stringLength($value);
        // Length between
        if (isset($params[1])) {
            return $length >= $params[0] && $length <= $params[1];
        }
        // Length same
        return $length == $params[0];
    }

    /**
     * Get the length of a string
     *
     * @param  string  $value
     * @return int
     */
    protected function stringLength($value)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value);
        }
        return strlen($value);
    }

    /**
     * Validate the size of a field is greater than a minimum value.
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    protected function validateMin($field, $value, $params)
    {
        return (int) $value >= $params[0];
    }

    /**
     * Validate the size of a field is less than a maximum value
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    protected function validateMax($field, $value, $params)
    {
        return (int) $value <= $params[0];
    }

    /**
     * Validate a field is contained within a list of values
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    protected function validateIn($field, $value, $params)
    {
        return in_array($value, $params[0]);
    }

    /**
     * Validate a field is not contained within a list of values
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    protected function validateNotIn($field, $value, $params)
    {
        return !$this->validateIn($field, $value, $params);
    }

    /**
     * Validate a field contains a given string
     *
     * @param  string $field
     * @param  mixed  $value
     * @param  array  $params
     * @return bool
     */
    protected function validateContains($field, $value, $params)
    {
        if (!isset($params[0])) {
            return false;
        }
        if (!is_string($params[0]) || !is_string($value)) {
            return false;
        }
        return (strpos($value, $params[0]) !== false);
    }

    /**
     * Validate that a field is a valid IP address
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    protected function validateIp($field, $value)
    {
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Validate that a field is a valid e-mail address
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    protected function validateEmail($field, $value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate that a field is a valid URL by syntax
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    protected function validateUrl($field, $value)
    {
        foreach ($this->validUrlPrefixes as $prefix) {
            if (strpos($value, $prefix) !== false) {
                return filter_var($value, FILTER_VALIDATE_URL) !== false;
            }
        }
        return false;
    }

    /**
     * Validate that a field is an active URL by verifying DNS record
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    protected function validateUrlActive($field, $value)
    {
        foreach ($this->validUrlPrefixes as $prefix) {
            if (strpos($value, $prefix) !== false) {
                $url = str_replace($prefix, '', strtolower($value));

                return checkdnsrr($url);
            }
        }
        return false;
    }

    /**
     * Validate that a field contains only alphabetic characters
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    protected function validateAlpha($field, $value)
    {
        return preg_match('/^([a-z])+$/i', $value);
    }

    /**
     * Validate that a field contains only alpha-numeric characters
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    protected function validateAlphaNum($field, $value)
    {
        return preg_match('/^([a-z0-9])+$/i', $value);
    }

    /**
     * Validate that a field contains only alpha-numeric characters, dashes, and underscores
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    protected function validateSlug($field, $value)
    {
        return preg_match('/^([-a-z0-9_-])+$/i', $value);
    }

    /**
     * Validate that a field passes a regular expression check
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    protected function validateRegex($field, $value, $params)
    {
        return preg_match($params[0], $value);
    }

    /**
     * Validate that a field is a valid date
     *
     * @param  string  $field
     * @param  mixed   $value
     * @return bool
     */
    protected function validateDate($field, $value)
    {
        return strtotime($value) !== false;
    }

    /**
     * Validate that a field matches a date format
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    protected function validateDateFormat($field, $value, $params)
    {
        $parsed = date_parse_from_format($params[0], $value);

        return $parsed['error_count'] === 0;
    }

    /**
     * Validate the date is before a given date
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    protected function validateDateBefore($field, $value, $params)
    {
        $vtime = ($value instanceof \DateTime) ? $value->getTimestamp() : strtotime($value);
        $ptime = ($params[0] instanceof \DateTime) ? $params[0]->getTimestamp() : strtotime($params[0]);
        return $vtime < $ptime;
    }

    /**
     * Validate the date is after a given date
     *
     * @param  string  $field
     * @param  mixed   $value
     * @param  array   $params
     * @return bool
     */
    protected function validateDateAfter($field, $value, $params)
    {
        $vtime = ($value instanceof \DateTime) ? $value->getTimestamp() : strtotime($value);
        $ptime = ($params[0] instanceof \DateTime) ? $params[0]->getTimestamp() : strtotime($params[0]);
        return $vtime > $ptime;
    }

    /**
     *  Get array of fields and data
     */
    public function data()
    {
        return $this->_fields;
    }

    /**
     *  Get array of error messages
     */
    public function errors($field = null)
    {
        if ($field !== null) {
            return isset($this->_errors[$field]) ? $this->_errors[$field] : false;
        }
        return $this->_errors;
    }

    /**
     *  Add an error to error messages array
     */
    public function error($field, $msg, array $params = array())
    {
        $values = array();
        // Printed values need to be in string format
        foreach ($params as $param) {
            if (is_array($param)) {
                $param = "['" . implode("', '", $param) . "']";
            }
            if ($param instanceof \DateTime) {
                $param = $param->format('Y-m-d');
            }
            $values[] = $param;
        }
        $this->_errors[$field][] = vsprintf($msg, $values);
    }

    /**
     * Specify validation message to use for error for the last validation rule
     */
    public function message($msg)
    {
        $this->_validations[count($this->_validations)-1]['message'] = $msg;
        return $this;
    }

    /**
     * Reset object properties
     */
    public function reset()
    {
        $this->_fields = array();
        $this->_errors = array();
        $this->_validations = array();
    }

    /**
     * Run validations and return boolean result
     *
     * @return boolean
     */
    public function validate()
    {
        foreach ($this->_validations as $v) {
            foreach ($v['fields'] as $field) {
                $value = isset($this->_fields[$field]) ? $this->_fields[$field] : null;

                // Callback is user-specified or assumed method on class
                if (isset(static::$_rules[$v['rule']])) {
                    $callback = static::$_rules[$v['rule']];
                } else {
                    $callback = array($this, 'validate' . ucfirst($v['rule']));
                }

                $result = call_user_func($callback, $field, $value, $v['params']);
                if(!$result) {
                    $this->error($field, $v['message'], $v['params']);
                }
            }
        }
        return count($this->errors()) === 0;
    }

    /**
     * Register new validation rule callback
     */
    public static function addRule($name, $callback, $message = self::ERROR_DEFAULT)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException("Second argument must be a valid callback. Given argument was not callable.");
        }

        static::$_rules[$name] = $callback;
        static::$_ruleMessages[$name] = $message;
    }

    /**
     * Convenience method to add a single validation rule
     */
    public function rule($rule, $fields)
    {
        if (!isset(static::$_rules[$rule])) {
            $ruleMethod = 'validate' . ucfirst($rule);
            if (!method_exists($this, $ruleMethod)) {
                throw new \InvalidArgumentException("Rule '" . $rule . "' has not been registered with " . __CLASS__ . "::addRule().");
            }
        }

        // Ensure rule has an accompanying message
        $message = isset(static::$_ruleMessages[$rule]) ? static::$_ruleMessages[$rule] : self::ERROR_DEFAULT;

        // Get any other arguments passed to function
        $params = array_slice(func_get_args(), 2);

        $this->_validations[] = array(
            'rule' => $rule,
            'fields' => (array) $fields,
            'params' => (array) $params,
            'message' => $message
        );
        return $this;
    }

    /**
     * Convenience method to add multiple validation rules with an array
     */
    public function rules($rules)
    {
        foreach ($rules as $ruleType => $params) {
            if (is_array($params)) {
                foreach ($params as $innerParams) {
                    array_unshift($innerParams, $ruleType);
                    call_user_func_array(array($this, "rule"), $innerParams);
                }
            } else {
                $this->rule($ruleType, $params);
            }
        }
    }
}
