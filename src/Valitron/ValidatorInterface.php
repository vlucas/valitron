<?php

namespace Valitron;

interface ValidatorInterface
{
    const V_REQUIRED = 'required';
    const V_EQULAS = 'equals';
    const V_DIFFERENT = 'different';
    const V_ACCEPTED = 'accepted';
    const V_NUMERIC = 'numeric';
    const V_INTEGER = 'integer';
    const V_LENGTH =  'length';
    const V_MIN = 'min';
    const V_MAX = 'max';
    const V_IN = 'in';
    const V_NOT_IN = 'notIn';
    const V_IP = 'ip';
    const V_IPV4 = 'ipv4';
    const V_IPV6 = 'ipv6';
    const V_EMAIL = 'email';
    const V_URL = 'url';
    const V_URL_ACTIVE = 'urlActive';
    const V_ALPHA = 'alpha';
    const V_ALPHA_NUM = 'alphaNum';
    const V_SLUG = 'slug';
    const V_REGEX = 'regex';
    const V_DATE = 'date';
    const V_DATE_FORMAT ='dateFormat';
    const V_DATE_BEFORE = 'dateBefore';
    const V_DATE_AFTER = 'dateAfter';
    const V_CONTAINS = 'contains';
    const V_BOOLEAN = 'boolean';
    const V_LENGTH_BETWEEN = 'lengthBetween';
    const V_CREDIT_CARD = 'creditCard';
    const V_LENGTH_MIN = 'lengthMin';
    const V_LENGTH_MAX = 'lengthMax';
    const V_INSTANCE_OF = 'instanceOf';
    const V_CONTAINS_UNIQUE = 'containsUnique';
    const V_SUBSET = 'subset';
}
