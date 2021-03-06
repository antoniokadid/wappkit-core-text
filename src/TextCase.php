<?php

namespace AntonioKadid\WAPPKitCore\Text;

/**
 * Class TextCase
 *
 * @package AntonioKadid\WAPPKitCore\Text
 */
abstract class TextCase
{
    public const UNKNOWN_CASE = 0;
    public const KEBAB_CASE = 1;
    public const LOWER_CAMEL_CASE = 2;
    public const SCREAMING_SNAKE_CASE = 3;
    public const SNAKE_CASE = 4;
    public const TRAIN_CASE = 5;
    public const UPPER_CAMEL_CASE = 6;

    /**
     * Convert input to kebab-case.
     *
     * @param string $input
     *
     * @return string
     */
    public static function toKebab(string $input): string
    {
        return self::process($input, self::KEBAB_CASE);
    }

    /**
     * Convert input to lowerCamelCase.
     *
     * @param string $input
     *
     * @return string
     */
    public static function toLowerCamel(string $input): string
    {
        return self::process($input, self::LOWER_CAMEL_CASE);
    }

    /**
     * Convert input to SCREAMING_SNAKE_CASE.
     *
     * @param string $input
     *
     * @return string
     */
    public static function toScreamingSnake(string $input): string
    {
        return self::process($input, self::SCREAMING_SNAKE_CASE);
    }

    /**
     * Convert input to snake_case.
     *
     * @param string $input
     *
     * @return string
     */
    public static function toSnake(string $input): string
    {
        return self::process($input, self::SNAKE_CASE);
    }

    /**
     * Convert to TRAIN-CASE
     *
     * @param string $input
     *
     * @return string
     */
    public static function toTrain(string $input): string
    {
        return self::process($input, self::TRAIN_CASE);
    }

    /**
     * Convert input to UpperCamelCase.
     *
     * @param string $input
     *
     * @return string
     */
    public static function toUpperCamel(string $input): string
    {
        return self::process($input, self::UPPER_CAMEL_CASE);
    }

    /**
     * Remove any character that is not alphanumeric or space.
     *
     * @param string $input
     *
     * @return string
     */
    private static function clean(string $input): string
    {
        return trim(preg_replace("/[^[:alnum:][:space:]]/u", '', $input));
    }

    /**
     * Pre-process input and create an array containing words and numbers.
     *
     * @param string $input
     *
     * @return array|null
     */
    private static function preProcess(string $input): ?array
    {
        $input = trim($input);

        $result = NULL;

        // check for Train and Kebab
        if (@preg_match("/^([[:upper:]]|[[:digit:]]|\\-)+$/u", $input) ||
            @preg_match("/^([[:lower:]]|[[:digit:]]|\\-)+$/u", $input)) {
            $result = preg_split("/\\-/u", $input, -1, PREG_SPLIT_NO_EMPTY);
        } // check for Screaming snake and Snake
        else if (@preg_match("/^([[:upper:]]|[[:digit:]]|\\_)+$/u", $input) ||
            @preg_match("/^([[:lower:]]|[[:digit:]]|\\_)+$/u", $input)) {
            $result = preg_split("/\\_/u", $input, -1, PREG_SPLIT_NO_EMPTY);
        } // check for Upper Camel and Lower Camel
        else if (!@preg_match("/^[[:upper:]][[:alnum:]]{0,}/u", $input) ||
            !@preg_match("/^[[:lower:]][[:alnum:]]{0,}/u", $input)) {
            $input = preg_replace_callback("/([[:lower:]])([[:upper:]])/u", function ($match) {
                return "{$match[1]} {$match[2]}";
            }, $input);

            $result = preg_split("/\\s+/u", $input, -1, PREG_SPLIT_NO_EMPTY);
        } else
            $result = preg_split("/\\s+/u", self::clean($input), -1, PREG_SPLIT_NO_EMPTY);

        return (!is_array($result) || empty($result)) ? NULL : $result;
    }

    /**
     * @param string $input
     * @param int    $targetStringCase
     *
     * @return string
     */
    private static function process(string $input, int $targetStringCase = self::UNKNOWN_CASE): string
    {
        $preProcessed = self::preProcess($input);
        if ($preProcessed == NULL)
            return $input;

        switch ($targetStringCase) {
            case self::KEBAB_CASE:
            {
                array_walk($preProcessed, function (string &$value) {
                    $value = mb_strtolower($value, 'UTF-8');
                });

                return implode('-', $preProcessed);
            }
            case self::LOWER_CAMEL_CASE:
            {
                array_walk($preProcessed, function (string &$value) {
                    $tmpValue = mb_strtolower($value, 'UTF-8');
                    $value = mb_strtoupper(mb_substr($tmpValue, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($tmpValue, 1, NULL, 'UTF-8');
                });

                $result = implode('', $preProcessed);

                return mb_strtolower(mb_substr($result, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($result, 1, NULL, 'UTF-8');
            }
            case self::SCREAMING_SNAKE_CASE:
            {
                array_walk($preProcessed, function (string &$value) {
                    $value = mb_strtoupper($value, 'UTF-8');
                });

                return implode('_', $preProcessed);
            }
            case self::SNAKE_CASE:
            {
                array_walk($preProcessed, function (string &$value) {
                    $value = mb_strtolower($value, 'UTF-8');
                });

                return implode('_', $preProcessed);
            }
            case self::TRAIN_CASE:
            {
                array_walk($preProcessed, function (string &$value) {
                    $value = mb_strtoupper($value, 'UTF-8');
                });

                return implode('-', $preProcessed);
            }
            case self::UPPER_CAMEL_CASE:
            {
                array_walk($preProcessed, function (string &$value) {
                    $tmpValue = mb_strtolower($value, 'UTF-8');
                    $value = mb_strtoupper(mb_substr($tmpValue, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($tmpValue, 1, NULL, 'UTF-8');
                });

                return implode('', $preProcessed);
            }
            default:
            {
                return $input;
            }
        }
    }
}