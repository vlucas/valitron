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


## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Make your changes
4. Run the tests, adding new ones for your own code if necessary (`phpunit`)
5. Commit your changes (`git commit -am 'Added some feature'`)
6. Push to the branch (`git push origin my-new-feature`)
7. Create new Pull Request

