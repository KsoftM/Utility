<?php

namespace ksoftm\utils\validator;

use DateTime;
use Exception;

class MegaValid
{
    /**
     * validate variables.
     * 
     * @example location [ $id, MegRule::new()->required() ]
     * 
     * @param array $megaValid
     *
     * @return bool
     */
    public static function validate(array $megaValid, ?array &$errors): bool
    {
        $errors = !empty($errors) ?: [];
        foreach ($megaValid as $data) {

            $megRule = $data[1];
            $data = $data[0];

            if ($megRule instanceof MegRule) {
                $have  = self::haveDataRender($data, $megRule, $errors);
                $datatype  = self::datatypeRender($data, $megRule, $errors);
                $depend  = self::dependRender($data, $megRule, $errors);

                if ($have && $datatype && $depend) {
                    $output = true;
                } else {
                    $output = false;
                    if (!$output && count($errors) == 0) {
                        $errors[] = 'Some server error.';
                    }
                    return $output;
                }
            }
        }
        return $output ?? false;
    }

    public static function haveDataRender(mixed $data, MegRule $rules, array &$errors): bool
    {
        $have = $rules->getHaveDataRules();
        $field = $rules->getField();

        //TODO  THE FIELD MUST BE IMPLEMENTED

        if ($have == MegRule::REQUIRED_TYPE) {
            $output = isset($data) || !empty($data);
            if ($output == false) {
                $errors[MegRule::REQUIRED_TYPE] = 'This is a required field.';
            }
        } elseif ($have == MegRule::NULLABLE_TYPE) {
            $output = true;
        } else {
            $output = false;
        }

        return $output ?? false;
    }

    public static function dataTypeRender(mixed $data, MegRule $rules, array &$errors): bool
    {
        $type = $rules->getDataTypeRules();
        $field = $rules->getField();

        //TODO  THE FIELD MUST BE IMPLEMENTED

        switch ($type) {
            case MegRule::INT_TYPE:
                $output  = filter_var($data, FILTER_SANITIZE_NUMBER_INT) && filter_var($data, FILTER_VALIDATE_INT);
                if ($output == false) {
                    $errors[MegRule::INT_TYPE] = 'This field must be a valid integer value.';
                }
                break;
            case MegRule::STRING_TYPE:
                $output  = filter_var($data, FILTER_SANITIZE_STRING) && filter_var($data, FILTER_DEFAULT);
                if ($output == false) {
                    $errors[MegRule::STRING_TYPE] = 'This field must be a valid string value.';
                }
                break;
            case MegRule::BOOLEAN_TYPE:
                $output  = filter_var($data, FILTER_VALIDATE_BOOL);
                if ($output == false) {
                    $errors[MegRule::BOOLEAN_TYPE] = 'This field must be a valid boolean value';
                }
                break;
            case MegRule::FLOAT_TYPE:
                $output  = filter_var($data, FILTER_SANITIZE_NUMBER_FLOAT) && filter_var($data, FILTER_VALIDATE_FLOAT);
                if ($output == false) {
                    $errors[MegRule::FLOAT_TYPE] = 'This field must be a valid float value.';
                }
                break;
            case MegRule::EMAIL_TYPE:
                $output  = filter_var($data, FILTER_SANITIZE_EMAIL) && filter_var($data, FILTER_VALIDATE_EMAIL);
                if ($output == false) {
                    $errors[MegRule::EMAIL_TYPE] = 'This field must be a valid email address.';
                }
                break;

            default:
                $output = false;
        }

        return $output ?? false;
    }

    public static function dependRender(mixed $data, MegRule $rules, array &$errors): bool
    {
        $depend = $rules->getDependRules();
        $type = $rules->getDataTypeRules();
        $field = $rules->getField();

        if (is_array($depend)) {
            foreach ($depend as $key => $value) {
                if (
                    $type == MegRule::INT_TYPE || $type == MegRule::FLOAT_TYPE ||
                    $type == MegRule::STRING_TYPE || $type == MegRule::EMAIL_TYPE
                ) {
                    $currentLength  = is_string($data) ? strlen($data) : $data;
                } else {
                    $value = 0;
                    $currentLength  = 'no';
                }

                if ($key == MegRule::MIN_TYPE) {

                    $output = ($value <= $currentLength);

                    if ($output == false) {

                        $errors[MegRule::MIN_TYPE] =
                            $type == MegRule::INT_TYPE || $type == MegRule::FLOAT_TYPE
                            ? "The minimum amount of the $field field must be $value."
                            : "The minimum length of the $field field must be $value.";
                    }
                } elseif ($key == MegRule::MAX_TYPE) {

                    $output = ($value >= $currentLength);

                    if ($output == false) {

                        $errors[MegRule::MIN_TYPE] =
                            $type == MegRule::INT_TYPE || $type == MegRule::FLOAT_TYPE
                            ? "The maximum amount of the $field field must be $value."
                            : "The maximum length of the $field field must be $value.";
                    }
                } elseif ($key == MegRule::SET_TYPE) {

                    if (is_array($value)) {

                        $output = in_array($data, $value);

                        if ($output == false) {
                            $errors[MegRule::SET_TYPE] = "This $field field must be contain '" . implode("' or '", $value) . "'.";
                        }
                    }
                } elseif ($key == MegRule::UNSIGNED_TYPE) {

                    if ($type != MegRule::INT_TYPE) {
                        throw new Exception("The unsigned rule must be work with an integer value.");
                    }

                    $output = ($data >= 0);

                    if ($output == false) {
                        $errors[MegRule::UNSIGNED_TYPE] = "This $field field must be unsigned integer value.";
                    }
                } elseif ($key == MegRule::PASSWORD_TYPE) {

                    if ($type != MegRule::STRING_TYPE) {
                        throw new Exception("The password rule must be work with an string value.");
                    }

                    $reg = preg_match('/^[a-zA-Z0-9 !"#$%&\'()*+,\-.\/:;<=>?@[\\\]^_`{|}~]*$/', $data) === false ? false : true;

                    $output = ($reg);

                    if ($output == false) {
                        $errors[MegRule::PASSWORD_TYPE] = "The password $field field must be contain valid characters.";
                    }
                } elseif ($key == MegRule::USER_NAME_TYPE) {

                    if ($type != MegRule::STRING_TYPE) {
                        throw new Exception("The username rule must be work with an string value.");
                    }

                    $reg = preg_match('/^[^0-9][a-zA-Z0-9_]*$/', $data) == false ? false : true;

                    $output = ($reg);

                    if ($output == false) {
                        $errors[MegRule::USER_NAME_TYPE] = "This $field field must only be contain letters(cap && small), numbers and underscore value.";
                    }
                } elseif ($key == MegRule::SLUG_TYPE) {

                    if ($type != MegRule::STRING_TYPE) {
                        throw new Exception("The slug rule must be work with an string value.");
                    }

                    $reg = preg_match('/^[^0-9][a-zA-Z0-9_-]*$/', $data) == false ? false : true;

                    $output = ($reg);

                    if ($output == false) {
                        $errors[MegRule::SLUG_TYPE] = "This $field field must be unsigned and also must be a valid integer value.";
                    }
                } elseif (
                    $key == MegRule::DATE_TYPE ||
                    $key == MegRule::TIME_TYPE ||
                    $key == MegRule::YEAR_TYPE ||
                    $key == MegRule::DATETIME_TYPE ||
                    $key == MegRule::TIMESTAMP_TYPE
                ) {

                    $output = DateTime::createFromFormat($value, $data);

                    if ($output == false) {
                        $errors[MegRule::DATETIME_TYPE] = "This $field field must be in valid date format.";
                    }
                } elseif ($key == MegRule::MATCH_TYPE) {

                    if (is_array($value)) {
                        $prop = $value[0] ?? false;
                        $value = $value[1];
                    }

                    $output = ($value === $data);

                    if ($output == false) {
                        $errors[MegRule::MATCH_TYPE] = ($prop == false)
                            ? "The fields must be match."
                            : "The $prop field must be match.";
                    }
                } else {
                    $output = false;
                }

                if ($output == false) {
                    return $output;
                }
            }
        }
        return $output ?? true;
    }
}
