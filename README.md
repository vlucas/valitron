## Valitron: Easy Validation That Doesn't Suck

Valitron is a simple, minimal and elegant stand-alone validation library
with NO dependencies. Valitron uses simple, straightforward validation
methods with a focus on readable and concise syntax. Valitron is the
simple and pragmatic validation library you've been loking for.

[![Build
Status](https://travis-ci.org/vlucas/valitron.png?branch=master)](https://travis-ci.org/vlucas/valitron)

## Why Valitron?

Valitron was created out of frustration with other validation libraries
that have dependencies on large components from other frameworks like
Symfony's HttpFoundation, pulling in a ton of extra files that aren't
really needed for basic validation. It also has purposefully simple
syntax used to run all validations in one call instead of individually
validating each value by instantiating new classes and validating values
one at a time like some other validation libraries require.

In short, Valitron is everything you've been looking for in a validation
library but haven't been able to find until now: simple pragmatic
syntax, lightweight code that makes sense, extensible for custom
callbacks and validations, well tested, and without dependencies. Let's
get started.

## Installation

Valitron uses [Composer](http://getcomposer.org) to install and update:

```
curl -s http://getcomposer.org/installer | php
php composer.phar require vlucas/valitron
```

The examples below use PHP 5.4 syntax, but Valitron works on PHP 5.3+.

## Usage

Usage is simple and straightforward. Just supply an array of data you
wish to validate, add some rules, and then call `validate()`. If there
are any errors, you can call `errors()` to get them.

```
$v = new Valitron\Validator(array('name' => 'Chester Tester'));
$v->rule('required', 'name');
if($v->validate()) {
    echo "Yay! We're all good!";
} else {
    // Errors
    print_r($v->errors());
}
```

Using this format, you can validate `$_POST` data directly and easily,
and can even apply a rule like `required` to an array of fields:

```
$v = new Valitron\Validator($_POST);
$v->rule('required', ['name', 'email']);
$v->rule('email', 'email');
if($v->validate()) {
    echo "Yay! We're all good!";
} else {
    // Errors
    print_r($v->errors());
}
```

## Built-in Validation Rules

 * `required` - Required field
 * `equals` - Field must match another field (email/password confirmation)
 * `different` - Field must be different than another field
 * `accepted` - Checkbox or Radio must be accepted (yes, on, 1, true)
 * `numeric` - Must be numeric
 * `integer` - Must be integer number
 * `length` - String must be certain length or between given lengths
 * `min` - Minimum
 * `max` - Maximum
 * `in` - Performs in_array check on given array values
 * `notIn` - Negation of `in` rule (not in array of values)
 * `ip` - Valid IP address
 * `email` - Valid email address
 * `url` - Valid URL 
 * `urlActive` - Valid URL with active DNS record
 * `alpha` - Alphabetic characters only
 * `alphaNum` - Alphabetic and numeric characters only
 * `slug` - URL slug characters (a-z, 0-9, -, _)
 * `regex` - Field matches given regex pattern
 * `date` - Field is a valid date
 * `dateFormat` - Field is a valid date in the given format
 * `dateBefore` - Field is a valid date and is before the given date
 * `dateAfter` - Field is a valid date and is after the given date

## Adding Custom Validation Rules

To add your own validation rule, use the `addRule` method with a rule
name, a custom callback or closure, and a error message to display in
case of an error. The callback provided should return boolean true or
false.

```
Valitron\Validation::addRule('alwaysFail', function($field, $value,
array $params) {
    return false;
}, 'Everything you do is wrong. You fail.');
```


## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Make your changes
4. Run the tests, adding new ones for your own code if necessary (`phpunit`)
5. Commit your changes (`git commit -am 'Added some feature'`)
6. Push to the branch (`git push origin my-new-feature`)
7. Create new Pull Request

