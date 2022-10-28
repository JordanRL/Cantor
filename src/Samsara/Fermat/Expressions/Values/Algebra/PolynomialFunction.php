<?php

namespace Samsara\Fermat\Expressions\Values\Algebra;

use Samsara\Exceptions\UsageError\IntegrityConstraint;
use Samsara\Fermat\Core\Types\Decimal;
use Samsara\Fermat\Core\Types\NumberCollection;
use Samsara\Fermat\Expressions\Enums\Functions;
use Samsara\Fermat\Expressions\Types\Base\Interfaces\Evaluateables\FunctionInterface;
use Samsara\Fermat\Expressions\Types\Expression;
use Samsara\Fermat\Core\Numbers;
use Samsara\Fermat\Core\Values\ImmutableDecimal;

/**
 * @package Samsara\Fermat\Expressions
 */
class PolynomialFunction extends Expression implements FunctionInterface
{

    /**
     * PolynomialFunction constructor.
     *
     * @param array|NumberCollection $coefficients
     *
     * @throws IntegrityConstraint
     */
    public function __construct(array|NumberCollection $coefficients)
    {
        parent::__construct(Functions::Polynomial);

        $sanitizedCoefficients = [];

        foreach ($coefficients as $exponent => $coefficient) {
            if (!is_int($exponent)) {
                throw new IntegrityConstraint(
                    'Keys in the $coefficients array must be integers',
                    'Only use integer keys for the $coefficients array',
                    'The key '.$exponent.' was found in the $coefficients array; an integer was expected'
                );
            }

            $fermatCoefficient = new ImmutableDecimal($coefficient);

            if (!$fermatCoefficient->isEqual(0)) {
                $sanitizedCoefficients[$exponent] = $fermatCoefficient;
            }
        }

        $this->terms = $sanitizedCoefficients;

        $this->expression = function($x): ImmutableDecimal {
            $value = Numbers::makeZero();

            /** @var ImmutableDecimal $xPart */
            $xPart = Numbers::makeOrDont(Numbers::IMMUTABLE, $x);

            foreach ($this->terms as $exponent => $coefficient) {
                if ($exponent == 0) {
                    $value = $value->add($coefficient);
                } else {
                    $term = $coefficient->multiply($xPart->pow($exponent));
                    $value = $value->add($term);
                }
            }

            if ($value->isEqual(0)) {
                $value = Numbers::makeOne();
            }

            return $value;
        };
    }

    /**
     * @param int|float|string|Decimal $x
     *
     * @return ImmutableDecimal
     */
    public function evaluateAt(int|float|string|Decimal $x): ImmutableDecimal
    {
        $answer = $this->expression;

        return $answer($x);
    }

    /**
     * @return FunctionInterface
     * @throws IntegrityConstraint
     */
    public function derivativeExpression(): FunctionInterface
    {
        $newCoefficients = [];

        /**
         * @var int              $exponent
         * @var ImmutableDecimal $coefficient
         */
        foreach ($this->terms as $exponent => $coefficient) {
            if ($exponent == 0) {
                continue;
            }

            $newCoefficients[$exponent-1] = $coefficient->multiply($exponent);
        }

        return new PolynomialFunction($newCoefficients);
    }

    /**
     * @param float|int|string|Decimal $C
     *
     * @return FunctionInterface
     * @throws IntegrityConstraint
     */
    public function integralExpression(float|int|string|Decimal $C = 0): FunctionInterface
    {
        $C = Numbers::make(Numbers::IMMUTABLE, $C);

        $newCoefficients = [];

        if (!$C->isEqual(0)) {
            $newCoefficients[0] = $C;
        }

        /**
         * @var int             $exponent
         * @var ImmutableDecimal $coefficient
         */
        foreach ($this->terms as $exponent => $coefficient) {
            $newExponent = $exponent+1;

            $newCoefficients[$newExponent] = $coefficient->divide($newExponent);
        }

        return new PolynomialFunction($newCoefficients);
    }

    public function describeShape(): array
    {

        $shape = [];

        /**
         * @var int             $exponent
         * @var ImmutableDecimal $coefficient
         */
        foreach ($this->terms as $exponent => $coefficient) {
            $shape[$exponent] = $coefficient->getValue();
        }

        return $shape;

    }

    /**
     * This function performs a FOIL expansion on a list of parameters.
     *
     * Assumptions:
     *      1. The coefficients are the numbers provided in the arrays
     *      2. The coefficients are listed in descending order of their exponent on the function variable. For example,
     *         if you were multiplying (2 + 3x)*(5 - 1x^2 + 1x), it would expect these inputs:
     *              -  [3, 2], [-1, 1, 5]
     *      3. If not all exponents are used continuously, a zero must be provided for the position that is skipped. For
     *         example, if one of the provided groups was 4x^2 + 2, it would expect: [4, 0, 2]
     *
     * @param int[]|float[]|Decimal[] $group1
     * @param int[]|float[]|Decimal[] $group2
     *
     * @return PolynomialFunction
     * @throws IntegrityConstraint
     */
    public static function createFromFoil(array $group1, array $group2): PolynomialFunction
    {
        $group1exp = count($group1) - 1;
        $group2exp = count($group2) - 1;

        /** @var ImmutableDecimal[] $finalCoefs */
        $finalCoefs = [];

        $group1 = Numbers::makeOrDont(Numbers::IMMUTABLE, $group1);
        $group2 = Numbers::makeOrDont(Numbers::IMMUTABLE, $group2);

        if ($group1exp <= $group2exp) {
            $largerGroup = $group2;
            $largerExp = $group2exp;
            $smallerGroup = $group1;
            $smallerExp = $group1exp;
        } else {
            $largerGroup = $group1;
            $largerExp = $group1exp;
            $smallerGroup = $group2;
            $smallerExp = $group2exp;
        }

        /**
         * @var int $key1
         * @var ImmutableDecimal $value1
         */
        foreach ($largerGroup as $key1 => $value1) {
            $largerKey = $largerExp - $key1;

            /**
             * @var int             $key2
             * @var ImmutableDecimal $value2
             */
            foreach ($smallerGroup as $key2 => $value2) {
                $smallerKey = $smallerExp - $key2;
                $newVal = $value1->multiply($value2);
                $newExp = $largerKey + $smallerKey;

                if (isset($finalCoefs[$newExp])) {
                    $finalCoefs[$newExp] = $finalCoefs[$newExp]->add($newVal);
                } else {
                    $finalCoefs[$newExp] = $newVal;
                }
            }
        }

        return new PolynomialFunction($finalCoefs);
    }

}