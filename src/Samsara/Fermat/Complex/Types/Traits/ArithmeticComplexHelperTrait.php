<?php

namespace Samsara\Fermat\Complex\Types\Traits;

use Samsara\Exceptions\SystemError\LogicalError\IncompatibleObjectState;
use Samsara\Exceptions\UsageError\IntegrityConstraint;
use Samsara\Fermat\Complex\Types\ComplexNumber;
use Samsara\Fermat\Complex\Values\ImmutableComplexNumber;
use Samsara\Fermat\Complex\Values\MutableComplexNumber;
use Samsara\Fermat\Coordinates\Values\PolarCoordinate;
use Samsara\Fermat\Core\Numbers;
use Samsara\Fermat\Core\Provider\ArithmeticProvider;
use Samsara\Fermat\Core\Values\ImmutableDecimal;
use Samsara\Fermat\Core\Values\ImmutableFraction;
use Samsara\Fermat\Core\Values\MutableDecimal;
use Samsara\Fermat\Core\Values\MutableFraction;
use Samsara\Fermat\Expressions\Values\Algebra\PolynomialFunction;

/**
 * @package Samsara\Fermat\Complex
 */
trait ArithmeticComplexHelperTrait
{

    /**
     * @param MutableDecimal|ImmutableDecimal|MutableFraction|ImmutableFraction $newRealPart
     * @param MutableDecimal|ImmutableDecimal|MutableFraction|ImmutableFraction $newImaginaryPart
     * @param int $scale
     * @return static|ImmutableComplexNumber|MutableComplexNumber|ImmutableDecimal
     * @throws IntegrityConstraint
     */
    protected function helperComplexAddSub(
        MutableDecimal|ImmutableDecimal|MutableFraction|ImmutableFraction $newRealPart,
        MutableDecimal|ImmutableDecimal|MutableFraction|ImmutableFraction $newImaginaryPart,
        int $scale
    ): MutableComplexNumber|ImmutableDecimal|ImmutableComplexNumber|static
    {
        if ($newRealPart->isEqual(0) xor $newImaginaryPart->isEqual(0)) {
            $newNum = $newRealPart->isEqual(0) ? $newImaginaryPart : $newRealPart;

            return new ImmutableDecimal($newNum->getValue(), $scale);
        }

        if ($newRealPart->isEqual(0) && $newImaginaryPart->isEqual(0)) {
            return new ImmutableDecimal('0', $scale);
        }

        return $this->setValue($newRealPart, $newImaginaryPart)->roundToScale($scale);
    }

    /**
     * @param ImmutableDecimal|ImmutableFraction $thisRealPart
     * @param ImmutableDecimal|ImmutableFraction $thisImaginaryPart
     * @param ImmutableDecimal|ImmutableFraction $thatRealPart
     * @param ImmutableDecimal|ImmutableFraction $thatImaginaryPart
     * @param int $scale
     * @return static|ImmutableComplexNumber|MutableComplexNumber|ImmutableDecimal|ImmutableFraction|MutableDecimal|MutableFraction
     * @throws IntegrityConstraint
     */
    protected function helperMulComplex(
        ImmutableDecimal|ImmutableFraction $thisRealPart,
        ImmutableDecimal|ImmutableFraction $thisImaginaryPart,
        ImmutableDecimal|ImmutableFraction $thatRealPart,
        ImmutableDecimal|ImmutableFraction $thatImaginaryPart,
        int $scale
    ): ImmutableFraction|MutableComplexNumber|ImmutableDecimal|MutableFraction|ImmutableComplexNumber|MutableDecimal|static
    {
        $foiled = PolynomialFunction::createFromFoil([
            $thisRealPart,
            $thisImaginaryPart
        ], [
            $thatRealPart,
            $thatImaginaryPart
        ]);

        $parts = $foiled->describeShape();

        $value = Numbers::makeZero()->setMode($this->getMode());

        foreach ($parts as $part) {
            $value = $value->add($part);
        }

        if ($value instanceof ComplexNumber) {
            return $this->setValue($value->getRealPart(), $value->getImaginaryPart())->roundToScale($scale);
        }

        return $value->roundToScale($scale);
    }

    /**
     * @param MutableDecimal|ImmutableDecimal|MutableFraction|ImmutableFraction $partA
     * @param MutableDecimal|ImmutableDecimal|MutableFraction|ImmutableFraction $partB
     * @param int $scale
     * @return static|ImmutableComplexNumber|MutableComplexNumber|ImmutableDecimal|ImmutableFraction|MutableDecimal|MutableFraction
     * @throws IntegrityConstraint
     */
    protected function helperMulDivPowReturn(
        MutableDecimal|ImmutableDecimal|MutableFraction|ImmutableFraction $partA,
        MutableDecimal|ImmutableDecimal|MutableFraction|ImmutableFraction $partB,
        int $scale
    ): ImmutableFraction|MutableComplexNumber|ImmutableDecimal|MutableFraction|ImmutableComplexNumber|MutableDecimal|static
    {
        $newRealPart = $partA->isReal() ? $partA : $partB;
        $newImaginaryPart = $partA->isImaginary() ? $partA : $partB;

        if ($newRealPart->isEqual(0) xor $newImaginaryPart->isEqual(0)) {
            return match ($newRealPart->isEqual(0)) {
                true => $newImaginaryPart,
                false => $newRealPart
            };
        } elseif ($newRealPart->isEqual(0) && $newImaginaryPart->isEqual(0)) {
            return (new ImmutableDecimal(0, $this->getScale()))->setMode($this->getMode());
        }

        return $this->setValue($newRealPart, $newImaginaryPart)->roundToScale($scale);
    }

    /**
     * @param ImmutableComplexNumber|ImmutableDecimal|ImmutableFraction $thatNum
     * @param ImmutableDecimal|ImmutableFraction $thatRealPart
     * @param ImmutableDecimal|ImmutableFraction $thatImaginaryPart
     * @return ImmutableDecimal[]
     * @throws IncompatibleObjectState
     * @throws IntegrityConstraint
     */
    protected function helperPowPolar(
        ImmutableComplexNumber|ImmutableDecimal|ImmutableFraction $thatNum,
        ImmutableDecimal|ImmutableFraction $thatRealPart,
        ImmutableDecimal|ImmutableFraction $thatImaginaryPart
    ): array
    {
        $internalScale = ($this->getScale() > $thatNum->getScale()) ? $this->getScale() : $thatNum->getScale();
        $internalScale += 5;

        $thisRho = $this->getDistanceFromOrigin()->truncateToScale($internalScale);
        $thisTheta = $this->getPolarAngle()->truncateToScale($internalScale);

        $e = Numbers::makeE($internalScale);

        $coef = $thisRho->pow($thatRealPart)->multiply($e->pow($thisTheta->multiply($thatImaginaryPart->asReal())->multiply(-1)));

        /** @var ImmutableDecimal $trigArg */
        $trigArg = $thisRho->ln()->multiply($thatImaginaryPart->asReal())->add($thatRealPart->multiply($thisTheta));

        $newRealPart = $trigArg->cos($internalScale)->multiply($coef);
        $newImaginaryPart = $trigArg->sin($internalScale)->multiply($coef)->multiply('i');

        return [$newRealPart, $newImaginaryPart];
    }

    /**
     * @param ImmutableComplexNumber|ImmutableDecimal|ImmutableFraction $thisNum
     * @param ImmutableDecimal $rotation
     * @param int $scale
     * @return ImmutableDecimal[]
     * @throws IntegrityConstraint
     */
    protected function helperPowPolarRotate(
        ImmutableComplexNumber|ImmutableDecimal|ImmutableFraction $thisNum,
        ImmutableDecimal $rotation,
        int $scale
    ): array
    {
        $rho = $thisNum->getDistanceFromOrigin();
        $theta = $thisNum->getPolarAngle();

        if (!$rho->isEqual(0)) {
            $rho = ArithmeticProvider::squareRoot($rho->getAsBaseTenRealNumber(), $scale);
        }

        $theta = $theta->multiply($rotation);

        $newPolar = new PolarCoordinate($rho, $theta);
        $newCartesian = $newPolar->asCartesian();

        $newRealPart = $newCartesian->getAxis('x');
        $newImaginaryPart = $newCartesian->getAxis('y');
        return [$newRealPart, $newImaginaryPart];
    }

    /**
     * @param ImmutableComplexNumber|ImmutableDecimal|ImmutableFraction $thisNum
     * @param ImmutableDecimal $roots
     * @param int $period
     * @param int $scale
     * @return ImmutableDecimal[]
     * @throws IncompatibleObjectState
     * @throws IntegrityConstraint
     */
    protected function helperRootsPolarRotate(
        ImmutableComplexNumber|ImmutableDecimal|ImmutableFraction $thisNum,
        ImmutableDecimal $roots,
        int $period,
        int $scale
    ): array
    {
        $rho = $thisNum->getDistanceFromOrigin();
        $theta = $thisNum->getPolarAngle();

        if (!$rho->isEqual(0)) {
            $rho = ArithmeticProvider::squareRoot($rho->getAsBaseTenRealNumber(), $scale);
        }

        $theta = $theta->divide($roots);

        if ($period > 0) {
            $period = Numbers::makeTau($scale)->setMode($thisNum->getMode())->multiply($period)->divide($roots);
            $theta = $theta->add($period);
        }

        $newPolar = new PolarCoordinate($rho, $theta);
        $newCartesian = $newPolar->asCartesian();

        $newRealPart = $newCartesian->getAxis('x');
        $newImaginaryPart = $newCartesian->getAxis('y');
        return [$newRealPart, $newImaginaryPart];
    }

}