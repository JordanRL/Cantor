<?php

declare(strict_types=1);

namespace Samsara\Fermat\Core\Types\Traits\Arithmetic;

use Samsara\Exceptions\SystemError\PlatformError\MissingPackage;
use Samsara\Exceptions\UsageError\IntegrityConstraint;
use Samsara\Fermat\Core\Enums\NumberBase;
use Samsara\Fermat\Core\Numbers;
use Samsara\Fermat\Core\Provider\ArithmeticProvider;
use Samsara\Fermat\Core\Types\Base\Interfaces\Numbers\DecimalInterface;

/**
 *
 */
trait ArithmeticScaleTrait
{

    /**
     * @param DecimalInterface $num
     * @return string
     */
    protected function addScale(DecimalInterface $num): string
    {

        $scale = ($this->getScale() > $num->getScale()) ? $this->getScale() : $num->getScale();

        return ArithmeticProvider::add($this->getAsBaseTenRealNumber(), $num->getAsBaseTenRealNumber(), $scale);

    }

    /**
     * @param DecimalInterface $num
     * @return string
     */
    protected function subtractScale(DecimalInterface $num): string
    {

        $scale = ($this->getScale() > $num->getScale()) ? $this->getScale() : $num->getScale();

        return ArithmeticProvider::subtract($this->getAsBaseTenRealNumber(), $num->getAsBaseTenRealNumber(), $scale);

    }

    /**
     * @param DecimalInterface $num
     * @return string
     */
    protected function multiplyScale(DecimalInterface $num): string
    {

        $scale = ($this->getScale() > $num->getScale()) ? $this->getScale() : $num->getScale();

        return ArithmeticProvider::multiply($this->getAsBaseTenRealNumber(), $num->getAsBaseTenRealNumber(), $scale);

    }

    /**
     * @param DecimalInterface $num
     * @param int|null $scale
     * @return string
     */
    protected function divideScale(DecimalInterface $num, ?int $scale): string
    {

        if (is_null($scale)) {
            $scale = ($this->getScale() > $num->getScale()) ? $this->getScale() : $num->getScale();
        }

        $scale = $scale + $this->numberOfLeadingZeros() + $num->numberOfLeadingZeros();

        return ArithmeticProvider::divide($this->getAsBaseTenRealNumber(), $num->getAsBaseTenRealNumber(), $scale+1);

    }

    /**
     * @param DecimalInterface $num
     * @return string
     * @throws IntegrityConstraint
     */
    protected function powScale(DecimalInterface $num): string
    {

        $scale = ($this->getScale() > $num->getScale()) ? $this->getScale() : $num->getScale();

        $scale += $this->numberOfDecimalDigits() + $num->numberOfDecimalDigits();

        if ($this->isWhole() && $num->isPositive() && $num->isWhole() && $num->isLessThan(PHP_INT_MAX)) {
            return gmp_strval(gmp_pow($this->getAsBaseTenRealNumber(), $num->asInt()));
        } elseif (!$num->isWhole() && !extension_loaded('decimal')) {
            $scale += 3;
            $thisNum = Numbers::make(Numbers::IMMUTABLE, $this->getValue(NumberBase::Ten), $scale);
            $thatNum = Numbers::make(Numbers::IMMUTABLE, $num->getValue(NumberBase::Ten), $scale);
            $exponent = $thatNum->multiply($thisNum->ln($scale, false));
            return $exponent->exp($scale, false)->getValue(NumberBase::Ten);
        }

        return ArithmeticProvider::pow($this->getAsBaseTenRealNumber(), $num->getAsBaseTenRealNumber(), $scale+1);

    }

    /**
     * @param int|null $scale
     * @return string
     */
    protected function sqrtScale(?int $scale): string
    {

        $scale = $scale ?? $this->getScale();

        return ArithmeticProvider::squareRoot($this->abs()->getAsBaseTenRealNumber(), $scale);

    }

}