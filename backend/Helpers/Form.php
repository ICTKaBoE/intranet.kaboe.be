<?php

namespace Helpers;

use Router\Helpers;
use Security\Input;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;

abstract class Form
{
    static function Validate($toValidate)
    {
        $invalid = $fields = [];

        foreach ($toValidate as $key => $value) {
            if (is_int($key)) $$value = Helpers::input()->post($value)?->getValue();
            else if (!is_int($key) && Arrays::getValue($value, "type") == "file") $$key = Helpers::input()->file($key);
            else $$key = Helpers::input()->post($key)?->getValue();

            $fields[is_int($key) ? $value : $key] = is_int($key) ? $$value : $$key;

            if (is_array($value)) {
                if (Arrays::getValue($value, 'placeholder')) $$key = str_replace(Arrays::getValue($value, 'placeholder'), "", $$key);
                if (Arrays::getValue($value, 'convert') || Arrays::getValue($value, 'type')) $$key = General::convert($$key, Arrays::getValue($value, "convert", Arrays::getValue($value, 'type')));
                if (Arrays::getValue($value, 'default')) $$key = $$key ?: Arrays::getValue($value, "default");
                $fields[$key] = $$key;

                if (Arrays::getValue($value, 'mandatory', false) == true) {
                    $precondMet = true;
                    if (Arrays::getValue($value, 'preconditions')) {
                        foreach (Arrays::getValue($value, 'preconditions') as $precondKey => $precondValue) {
                            if (!Strings::equalsIgnoreCase(is_string($$precondKey) ? $$precondKey : json_encode($$precondKey), is_string($precondValue) ? $precondValue : json_encode($precondValue))) $precondMet = false;
                        }
                    }

                    if ($precondMet) {
                        if (Arrays::getValue($value, 'type') == "file") {
                            if ($$key[0]->getSize() == 0) array_push($invalid, $key);
                        } else {
                            if (!Input::check($$key, Arrays::getValue($value, 'type', Input::INPUT_TYPE_STRING)) || Input::empty($$key)) array_push($invalid, $key);
                        }
                    }
                }
            }
        }

        return [$invalid, $fields];
    }
}
