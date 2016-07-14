<?php

namespace Samsara\Fermat\Values\Base;

interface NumberInterface
{

    /**
     * @param int $base
     * @return NumberInterface
     */
    public function convertToBase($base);

    /**
     * @param $mod
     *
     * @return NumberInterface
     */
    public function modulo($mod);

    /**
     * @param $mod
     *
     * @return NumberInterface
     */
    public function continuousModulo($mod);

    /**
     * @param $value
     *
     * @return int
     */
    public function compare($value);

    /**
     * @return NumberInterface
     */
    public function abs();

    /**
     * @return string
     */
    public function absValue();

    /**
     * @return bool
     */
    public function isNegative();

    /**
     * @return bool
     */
    public function isPositive();

    /**
     * @return bool
     */
    public function isNatural();

    /**
     * @param int $decimals
     *
     * @return NumberInterface
     */
    public function round($decimals = 0);

    /**
     * @return NumberInterface
     */
    public function ceil();

    /**
     * @return NumberInterface
     */
    public function floor();

    /**
     * @return string
     */
    public function getValue();

    /**
     * @return int
     */
    public function getBase();

    /**
     * @return int|null
     */
    public function getPrecision();

    /**
     * @param $num
     *
     * @return NumberInterface
     */
    public function add($num);

    /**
     * @param $num
     *
     * @return NumberInterface
     */
    public function subtract($num);

    /**
     * @param $num
     *
     * @return NumberInterface
     */
    public function multiply($num);

    /**
     * @param $num
     *
     * @return NumberInterface
     */
    public function divide($num);

    /**
     * @return NumberInterface
     */
    public function factorial();

    /**
     * @return NumberInterface
     */
    public function doubleFactorial();

    /**
     * @return NumberInterface
     */
    public function semiFactorial();

    /**
     * @param $num
     *
     * @return NumberInterface
     */
    public function pow($num);

    /**
     * @return NumberInterface
     */
    public function sqrt();

    /**
     * @param int  $mult
     * @param int  $div
     * @param null $precision
     *
     * @return NumberInterface
     */
    public function sin($mult = 1, $div = 1, $precision = null);

    /**
     * @param int  $mult
     * @param int  $div
     * @param null $precision
     *
     * @return NumberInterface
     */
    public function cos($mult = 1, $div = 1, $precision = null);

    /**
     * @return int|bool
     */
    public function convertForModification();

    /**
     * @param $oldBase
     *
     * @return NumberInterface
     */
    public function convertFromModification($oldBase);

    /**
     * @param $precision
     *
     * @return NumberInterface
     */
    public function roundToPrecision($precision);

    /**
     * @return int
     */
    public function numberOfLeadingZeros();

    /**
     * @param int|string|NumberInterface $value
     *
     * @return bool
     */
    public function equals($value);

    /**
     * @param int|string|NumberInterface $value
     *
     * @return bool
     */
    public function greaterThan($value);

    /**
     * @param int|string|NumberInterface $value
     *
     * @return bool
     */
    public function lessThan($value);

    /**
     * @param int|string|NumberInterface $value
     *
     * @return bool
     */
    public function greaterThanOrEqualTo($value);

    /**
     * @param int|string|NumberInterface $value
     *
     * @return bool
     */
    public function lessThanOrEqualTo($value);

}