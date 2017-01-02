<?php
namespace Valitron;


interface ValidationSetInterface
{
    /**
     * Convenience method to add a single validation rule
     *
     * @param  string $rule
     * @param  array|string $fields fields
	 * @param  mixed ... $arguments
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function rule($rule, $fields);

    /**
     * @param  string $value
     * @internal param array $labels
     * @return $this
     */
    public function label($value);

    /**
     * @param  array  $labels
     * @return string
     */
    public function labels($labels);

    /**
     * Specify validation message to use for error for the last validation rule
     *
     * @param  string $msg
     * @return $this
     */
    public function message($msg);

    /**
     * Convenience method to add multiple validation rules with an array
     *
     * @param array $rules
     */
    public function rules($rules);

	/**
	 * Chain another ValidationSet to last set rule
	 *
	 * @param $rule
	 * @param $_
	 * @return $this
	 */
    public function condition($rule, $_);

    /**
     * @return array
     */
    public function getValidations();

    /**
     * @param $name
     * @param $field
     * @return boolean
     */
    public function hasRule($name, $field);

    /**
     * @return array
     */
    public function getLabels();

}