<?php
namespace Valitron;

use Valitron\Validator;

class ValidationSet implements ValidationSetInterface
{
    /**
     * @var Validator
     */
    protected $parentValidator;
    /**
     * @var array
     */
    protected $validations = array();
    /**
     * @var array
     */
    protected $labels  = array();

    public function __construct(Validator $validator)
    {
        $this->parentValidator = $validator;
    }

    /**
     * @param string $rule
     * @param string|array $fields
	 * @param mixed ... $arguments
     * @return $this
     */
    public function rule($rule, $fields)
    {
		// Get any other arguments passed to function
        $params = array_slice(func_get_args(), 2);

		if (is_callable($rule)
			&& !(is_string($rule) && $this->parentValidator->hasValidator($rule)))
		{
			$name = $this->parentValidator->getUniqueRuleName($fields);
			$msg = isset($params[0]) ? $params[0] : null;
			$this->parentValidator->addInstanceRule($name, $rule, $msg);
			$rule = $name;
		}

		$rules = $this->parentValidator->getRules();
		if (!isset($rules[$rule])) {
            $ruleMethod = 'validate' . ucfirst($rule);
            if (!method_exists($this->parentValidator, $ruleMethod)) {
                throw new \InvalidArgumentException("Rule '" . $rule . "' has not been registered with {".get_class($this->parentValidator)."::addRule().");
            }
        }

        // Ensure rule has an accompanying message
		$msgs = $this->parentValidator->getRuleMessages();
		$parentValidatorClass = get_class($this->parentValidator);
		$message = isset($msgs[$rule]) ? $msgs[$rule] : $parentValidatorClass::ERROR_DEFAULT;

        $newRule = array(
            'rule' => $rule,
            'fields' => (array)$fields,
            'params' => (array)$params,
            'message' => '{field} ' . $message
        );

        $this->validations[] = $newRule;

        return $this;
    }

	/**
	 * @param $rule
	 * @param $_
	 * @return $this
	 * @internal param callable $callback
	 */
    public function condition($rule, $_)
    {
		$params = func_get_args();
		$callback = array_pop($params);

        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Argument must be a valid callback. Given argument was not callable.');
        }

		$dependent = new self($this->parentValidator);

        $callback($dependent);

		call_user_func_array(array($this, 'rule'), $params);
        $this->validations[count($this->validations) - 1]['dependent'] = $dependent;

        return $this;
    }

    /**
     * @param string $msg
     * @return $this
     */
    public function message($msg)
    {
        $lastValidation = count($this->validations) - 1;
        $this->validations[$lastValidation]['message'] = $msg;

        return $this;
    }

    /**
     * @param  string $value
     * @internal param array $labels
     * @return $this
     */
    public function label($value)
    {
        $lastValidation = count($this->validations) - 1;

        $lastRules = $this->validations[$lastValidation]['fields'];

        $this->labels(array($lastRules[0] => $value));

        return $this;
    }

    /**
     * @param  array $labels
     * @return string
     */
    public function labels($labels)
    {
        $this->labels = array_merge($this->labels, $labels);

        return $this;
    }

    /**
     * Convenience method to add multiple validation rules with an array
     *
     * @param array $rules
     */
    public function rules($rules)
    {
        foreach ($rules as $ruleType => $params) {
            if (is_array($params)) {
                foreach ($params as $innerParams) {
                    array_unshift($innerParams, $ruleType);
                    call_user_func_array(array($this, 'rule'), $innerParams);
                }
            } else {
                $this->rule($ruleType, $params);
            }
        }
    }

    protected function ruleExists($rule) {
        return forward_static_call(array(get_class($this->parentValidator), 'ruleExists'), $rule);
    }

    protected function getRuleMessage($rule) {
        return forward_static_call(array(get_class($this->parentValidator), 'getRuleMessage'), $rule);
    }

    /**
     * @return array
     */
    public function getValidations()
    {
        return $this->validations;
    }

    /**
     * @param $name
     * @param $field
     * @return boolean
     */
    public function hasRule($name, $field)
    {
        foreach ($this->validations as $validation) {
            if ($validation['rule'] == $name) {
                if (in_array($field, $validation['fields'])) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }
}