<?php

namespace ksoftm\system\utils\validator;

/**
 * MegaValid rule class.
 */
class MegRule
{
    /** @var array $args rules arguments. */
    protected array $args = [];

    /** @var string $fieldName owner fo the field. */
    protected ?string $fieldName = null;

    //<<----------->> set of have data type <<----------->>//

    public const REQUIRED_TYPE = 100;
    public const NULLABLE_TYPE = 101;

    //<<-----X----->> set of have data type <<-----X----->>//

    //<<----------->> set fo datatype <<----------->>//

    public const INT_TYPE = 200;
    public const STRING_TYPE = 201;
    public const BOOLEAN_TYPE = 202;
    public const FLOAT_TYPE = 203;

    //<<-----X----->> set fo datatype <<-----X----->>//

    //<<----------->> set of depend argument type <<----------->>//

    public const DATE_TYPE = 300;
    public const TIME_TYPE = 301;
    public const YEAR_TYPE = 302;
    public const DATETIME_TYPE = 303;
    public const TIMESTAMP_TYPE = 304;
    public const MATCH_TYPE = 305;
    public const UNSIGNED_TYPE = 306;
    public const MIN_TYPE = 307;
    public const MAX_TYPE = 308;
    public const SET_TYPE = 309;
    public const USER_NAME_TYPE = 310;
    public const SLUG_TYPE = 311;
    public const PASSWORD_TYPE = 312;
    public const EMAIL_TYPE = 313;

    //<<-----X----->> set of depend argument type <<-----X----->>//


    //<<----------->> set fo main argument type <<----------->>//

    public const HAVE_DATA_ARGS_TYPE = 400;
    public const DATATYPE_ARGS_TYPE = 401;
    public const DEPEND_ARGS_TYPE = 402;

    //<<-----X----->> set fo main argument type <<-----X----->>//

    /**
     * Class constructor.
     */
    public function __construct(string $fieldName = null)
    {
        // $this->fieldName = strtolower($fieldName);
        $this->fieldName = $fieldName;
        $this->nullable()->string();
    }

    public function getHaveDataRules(): int|false
    {
        return $this->args[MegRule::HAVE_DATA_ARGS_TYPE] ?? false;
    }
    public function getDataTypeRules(): int|false
    {
        return $this->args[MegRule::DATATYPE_ARGS_TYPE] ?? false;
    }
    public function getDependRules(): array|false
    {
        return $this->args[MegRule::DEPEND_ARGS_TYPE] ?? false;
    }

    public function getRules(): array
    {
        return $this->args;
    }

    public function getField(): string|false
    {
        return ucfirst($this->fieldName) ?? false;
    }

    public function getRawField(): string|false
    {
        return $this->fieldName ?? false;
    }

    /**
     * create new [MegRule] statically
     * 
     * nullable and string type rules are default init rules.
     *
     * @return MegRule
     */
    public static function new(string $displayName = null): MegRule
    {
        return new MegRule($displayName);
    }

    public function required(): MegRule
    {
        $this->args[MegRule::HAVE_DATA_ARGS_TYPE] = MegRule::REQUIRED_TYPE;

        return $this;
    }

    public function nullable(): MegRule
    {
        $this->args[MegRule::HAVE_DATA_ARGS_TYPE] = MegRule::NULLABLE_TYPE;

        return $this;
    }

    public function int(): MegRule
    {
        $this->args[MegRule::DATATYPE_ARGS_TYPE] = MegRule::INT_TYPE;

        return $this;
    }

    public function string(): MegRule
    {
        $this->args[MegRule::DATATYPE_ARGS_TYPE] = MegRule::STRING_TYPE;

        return $this;
    }
    public function boolean(): MegRule
    {
        $this->args[MegRule::DATATYPE_ARGS_TYPE] = MegRule::BOOLEAN_TYPE;

        return $this;
    }

    public function float(): MegRule
    {
        $this->args[MegRule::DATATYPE_ARGS_TYPE] = MegRule::FLOAT_TYPE;

        return $this;
    }

    public function email(): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::EMAIL_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::EMAIL_TYPE]
            ?? MegRule::EMAIL_TYPE;

        return $this;
    }

    public function unsigned(): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::UNSIGNED_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::UNSIGNED_TYPE]
            ?? MegRule::UNSIGNED_TYPE;

        return $this;
    }

    /**
     * @aloud [a-zA-Z0-9 !"#$%&\'()*+,\-.\/:;<=>?@[\\\]^_`{|}~]
     *
     * @return MegRule
     */
    public function password(): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::PASSWORD_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::PASSWORD_TYPE]
            ?? MegRule::PASSWORD_TYPE;

        return $this;
    }

    /**
     * username does not start in numbers
     * @aloud [^0-9][a-zA-Z0-9_]
     *
     * @return MegRule
     */
    public function userName(): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::USER_NAME_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::USER_NAME_TYPE]
            ?? MegRule::SLUG_TYPE;

        return $this;
    }

    /**
     * SLUG does not start in numbers
     * @aloud [^0-9][a-zA-Z0-9_-]
     *
     * @return MegRule
     */
    public function slug(): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::SLUG_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::SLUG_TYPE]
            ?? MegRule::SLUG_TYPE;

        return $this;
    }

    public function date(string $format = 'd n Y'): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::DATE_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::DATE_TYPE]
            ?? $format;

        return $this;
    }

    public function time(string $format = 'H:i:s'): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::TIME_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::TIME_TYPE]
            ?? $format;

        return $this;
    }

    public function year(string $format = 'Y'): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::YEAR_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::YEAR_TYPE]
            ?? $format;

        return $this;
    }

    public function dateTime(string $format = 'd M Y H:i:s'): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::DATETIME_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::DATETIME_TYPE]
            ?? $format;

        return $this;
    }

    public function timestamp(string $format = 'D, d M Y H:i:s'): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::TIMESTAMP_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::TIMESTAMP_TYPE]
            ?? $format;

        return $this;
    }

    public function min(int|float $amount = 0): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::MIN_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::MIN_TYPE]
            ?? $amount;

        return $this;
    }

    public function max(int|float $amount = 255): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::MAX_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::MAX_TYPE]
            ?? $amount;

        return $this;
    }

    public function set(array $data): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::SET_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::SET_TYPE]
            ?? $data;

        return $this;
    }

    public function match(string $name, mixed $propertyName): MegRule
    {
        $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::MATCH_TYPE] =
            $this->args[MegRule::DEPEND_ARGS_TYPE][MegRule::MATCH_TYPE]
            ?? [$name, $propertyName];

        return $this;
    }
}
