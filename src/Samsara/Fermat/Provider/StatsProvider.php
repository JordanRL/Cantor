<?php

namespace Samsara\Fermat\Provider;

use Ds\Vector;
use Samsara\Exceptions\UsageError\IntegrityConstraint;
use Samsara\Exceptions\SystemError\LogicalError\IncompatibleObjectState;
use Samsara\Exceptions\UsageError\OptionalExit;
use Samsara\Fermat\Numbers;
use Samsara\Fermat\Types\Base\Interfaces\Numbers\NumberInterface;
use Samsara\Fermat\Types\Base\Interfaces\Numbers\DecimalInterface;
use Samsara\Fermat\Types\Base\Interfaces\Numbers\FractionInterface;
use Samsara\Fermat\Values\ImmutableDecimal;
use Samsara\Fermat\Values\ImmutableFraction;

class StatsProvider
{

    /**
     * @var Vector
     */
    protected static $inverseErrorCoefs;

    /**
     * @param $x
     *
     * @return NumberInterface
     * @throws IntegrityConstraint
     * @throws OptionalExit|\ReflectionException
     */
    public static function normalCDF($x): ImmutableDecimal
    {
        $x = Numbers::makeOrDont(Numbers::IMMUTABLE, $x);

        $precision = $x->getPrecision();
        $internalPrecision = $precision+2;

        $pi = Numbers::makePi($internalPrecision);
        $e = Numbers::makeE($internalPrecision);
        $one = Numbers::makeOne($internalPrecision);

        $eExponent = Numbers::make(Numbers::IMMUTABLE, $x->getValue());
        $eExponent = $eExponent->pow(2)->divide(2)->multiply(-1);

        $answer = Numbers::make(Numbers::IMMUTABLE, 0.5);
        $answer = $answer->add(
            $one->divide($pi->multiply(2)->sqrt())
                ->multiply($e->pow($eExponent))
                ->multiply(SeriesProvider::maclaurinSeries(
                    $x,
                    function ($n) {
                        return Numbers::makeOne();
                    },
                    function ($n) {
                        return SequenceProvider::nthOddNumber($n);
                    },
                    function ($n) {
                        return SequenceProvider::nthOddNumber($n)->doubleFactorial();
                    },
                    0,
                    $internalPrecision
                ))
        );

        return $answer->truncateToPrecision($precision);

    }

    /**
     * @param $x
     *
     * @return DecimalInterface|NumberInterface
     * @throws IntegrityConstraint
     * @throws OptionalExit
     */
    public static function complementNormalCDF($x): ImmutableDecimal
    {
        $p = self::normalCDF($x);
        $one = Numbers::makeOne();

        return $one->subtract($p);
    }

    /**
     * @param $x
     *
     * @return DecimalInterface|FractionInterface|NumberInterface|ImmutableDecimal
     * @throws IntegrityConstraint
     * @throws OptionalExit
     */
    public static function gaussErrorFunction($x): ImmutableDecimal
    {

        $x = Numbers::makeOrDont(Numbers::IMMUTABLE, $x);

        $precision = $x->getPrecision();
        $internalPrecision = $precision + 2;

        $answer = Numbers::makeOne($internalPrecision);
        $pi = Numbers::makePi($internalPrecision);

        $answer = $answer->multiply(2)->divide($pi->sqrt());

        $answer = $answer->multiply(
            SeriesProvider::maclaurinSeries(
                $x,
                function ($n) {
                    $negOne = Numbers::make(Numbers::IMMUTABLE, -1);

                    return $negOne->pow($n);
                },
                function ($n) {
                    return SequenceProvider::nthOddNumber($n);
                },
                function ($n) {
                    $n = Numbers::makeOrDont(Numbers::IMMUTABLE, $n);

                    return $n->factorial()->multiply(SequenceProvider::nthOddNumber($n->asInt()));
                },
                0,
                $internalPrecision
            )
        );

        return $answer->truncateToPrecision($precision);

    }

    /**
     * @param     $p
     * @param int $precision
     *
     * @return DecimalInterface|NumberInterface|ImmutableDecimal
     * @throws IntegrityConstraint
     * @throws OptionalExit
     */
    public static function inverseNormalCDF($p, int $precision = 10): ImmutableDecimal
    {
        $p = Numbers::makeOrDont(Numbers::IMMUTABLE, $p);

        $precision = $precision ?? $p->getPrecision();
        $internalPrecision = $precision + 2;

        $two = Numbers::make(Numbers::IMMUTABLE, 2, $internalPrecision);
        $invErfArg = $two->multiply($p)->subtract(1);

        return StatsProvider::inverseGaussErrorFunction($invErfArg, $internalPrecision)->multiply($two->sqrt($internalPrecision))->roundToPrecision($precision);
    }

    /**
     * @param $n
     * @param $k
     *
     * @return DecimalInterface|NumberInterface|ImmutableDecimal
     * @throws IntegrityConstraint
     * @throws IncompatibleObjectState
     */
    public static function binomialCoefficient($n, $k): ImmutableDecimal
    {

        $n = Numbers::makeOrDont(Numbers::IMMUTABLE, $n);
        $k = Numbers::makeOrDont(Numbers::IMMUTABLE, $k);

        if ($k->isLessThan(0) || $n->isLessThan($k)) {
            throw new IntegrityConstraint(
                '$k must be larger or equal to 0 and less than or equal to $n',
                'Provide valid $n and $k values such that 0 <= $k <= $n',
                'For $n choose $k, the values of $n and $k must satisfy the inequality 0 <= $k <= $n'
            );
        }

        if (!$n->isInt() || !$k->isInt()) {
            throw new IntegrityConstraint(
                '$k and $n must be whole numbers',
                'Provide whole numbers for $n and $k',
                'For $n choose $k, the values $n and $k must be whole numbers'
            );
        }

        return $n->factorial()->divide($k->factorial()->multiply($n->subtract($k)->factorial()));

    }

    public static function inverseErrorCoefficients(int $termIndex): ImmutableFraction
    {

        $terms =& static::$inverseErrorCoefs;

        if (is_null(static::$inverseErrorCoefs)) {
            $terms = new Vector();
            $terms->push(new ImmutableFraction(Numbers::makeOne(), Numbers::makeOne()));
            $terms->push(new ImmutableFraction(Numbers::makeOne(), Numbers::makeOne()));
        }

        if ($terms->offsetExists($termIndex)) {
            return $terms->get($termIndex);
        }

        $nextTerm = $terms->count();

        for ($k = $nextTerm;$k <= $termIndex;$k++) {
            $termValue = new ImmutableFraction(new ImmutableDecimal('0'), new ImmutableDecimal('1'));
            for ($m = 0;$m <= ($k - 1);$m++) {
                $part1 = $terms->get($m);
                $part2 = $terms->get($k - 1 - $m);
                $part3 = $part1->multiply($part2);
                $part4 = ($m + 1)*($m*2 + 1);
                $part5 = $part3->divide($part4);
                $termValue = $termValue->add($part5);
            }

            $termValue = $termValue->simplify();

            $terms->push($termValue);
        }

        return $terms->get($termIndex);

    }

    /**
     * @param $z
     * @param int $precision
     *
     * @return ImmutableDecimal
     * @throws IntegrityConstraint
     * @throws OptionalExit
     * @throws \ReflectionException
     */
    public static function inverseGaussErrorFunction($z, int $precision = 10): ImmutableDecimal
    {

        $z = Numbers::makeOrDont(Numbers::IMMUTABLE, $z);

        $precision = $precision ?? $z->getPrecision();
        $internalPrecision = $precision + 1;

        $pi = Numbers::makePi($internalPrecision);

        $answer = SeriesProvider::maclaurinSeries(
            $z,
            function ($n) use ($pi) {
                if ($n > 0) {
                    return $pi->pow($n)->multiply(StatsProvider::inverseErrorCoefficients($n));
                }

                return Numbers::makeOne();
            },
            function ($n) {
                return SequenceProvider::nthOddNumber($n);
            },
            function ($n) {
                if ($n > 0) {
                    $extra = Numbers::make(Numbers::IMMUTABLE, 2)->pow(SequenceProvider::nthEvenNumber($n));
                } else {
                    $extra = Numbers::makeOne();
                }

                return SequenceProvider::nthOddNumber($n)->multiply($extra);
            },
            0,
            $internalPrecision
        );

        $answer = $answer->multiply($pi->sqrt($internalPrecision)->divide(2, $internalPrecision));

        return $answer->roundToPrecision($precision);

    }

}