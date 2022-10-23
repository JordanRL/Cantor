<?php

namespace Samsara\Fermat\Complex\Values;

use PHPUnit\Framework\TestCase;
use Samsara\Exceptions\UsageError\IntegrityConstraint;
use Samsara\Fermat\Core\Types\Base\Interfaces\Numbers\NumberInterface;
use Samsara\Fermat\Core\Values\ImmutableDecimal;

class ArithmeticAutoTest extends TestCase
{

    /*
     * add()
     */

    public function additionImmutableComplexProvider(): array
    {
        $five = new ImmutableDecimal('5');
        $fiveI = new ImmutableDecimal('5i');
        $negFive = new ImmutableDecimal('-5');
        $negFiveI = new ImmutableDecimal('-5i');
        $zero = new ImmutableDecimal('0');
        $zeroI = new ImmutableDecimal('0i');
        $one = new ImmutableDecimal('1');
        $oneI = new ImmutableDecimal('1i');
        $tenPowThirty = new ImmutableDecimal('1000000000000000000000000000000');
        $tenPowThirtyI = new ImmutableDecimal('1000000000000000000000000000000i');
        $tenScale = new ImmutableDecimal('0.0000000001');
        $tenScaleI = new ImmutableDecimal('0.0000000001i');
        $twelveScale = new ImmutableDecimal('0.000000000001');
        $twelveScaleI = new ImmutableDecimal('0.000000000001i');

        $a = new ImmutableComplexNumber($five, $fiveI);
        $b = new ImmutableComplexNumber($negFive, $negFiveI);
        $c = new ImmutableComplexNumber($five, $negFiveI);
        $d = new ImmutableComplexNumber($negFive, $fiveI);
        $e = new ImmutableComplexNumber($one, $tenPowThirtyI);
        $f = new ImmutableComplexNumber($tenPowThirty, $oneI);
        $g = new ImmutableComplexNumber($one, $tenScaleI);
        $h = new ImmutableComplexNumber($tenScale, $oneI);
        $i = new ImmutableComplexNumber($one, $twelveScaleI);
        $j = new ImmutableComplexNumber($twelveScale, $oneI);
        $k = new ImmutableComplexNumber($one, $zeroI);
        $l = new ImmutableComplexNumber($zero, $oneI);

        return [
            'IComplex (5+5i)+(5+5i)' => [$a, $a, '10+10i', ImmutableComplexNumber::class],
            'IComplex (5+5i)+(-5-5i)' => [$a, $b, '0', ImmutableDecimal::class],
            'IComplex (5+5i)+(5-5i)' => [$a, $c, '10', ImmutableDecimal::class],
            'IComplex (5+5i)+(-5+5i)' => [$a, $d, '10i', ImmutableDecimal::class],
            'IComplex (5+5i)+(5i)' => [$a, $fiveI, '5+10i', ImmutableComplexNumber::class],
            'IComplex (5i)+(5+5i)' => [$fiveI, $a, '5+10i', ImmutableComplexNumber::class],
            'IComplex (1+1000000000000000000000000000000i)+(5+5i)' => [$e, $a, '6+1000000000000000000000000000005i', ImmutableComplexNumber::class],
            'IComplex (1000000000000000000000000000000+1i)+(5+5i)' => [$f, $a, '1000000000000000000000000000005+6i', ImmutableComplexNumber::class],
            'IComplex (1+0.0000000001i)+(5+5i)' => [$g, $a, '6+5.0000000001i', ImmutableComplexNumber::class],
            'IComplex (0.0000000001+1i)+(5+5i)' => [$h, $a, '5.0000000001+6i', ImmutableComplexNumber::class],
            'IComplex (1+0.000000000001i)+(5+5i)' => [$i, $a, '6+5.000000000001i', ImmutableComplexNumber::class],
            'IComplex (0.000000000001+1i)+(5+5i)' => [$j, $a, '5.000000000001+6i', ImmutableComplexNumber::class],
            'IComplex (1+0i)+(5+5i)' => [$k, $a, '6+5i', ImmutableComplexNumber::class],
            'IComplex (0+1i)+(5+5i)' => [$l, $a, '5+6i', ImmutableComplexNumber::class],
        ];
    }

    public function additionMutableComplexProvider(): array
    {
        $five = new ImmutableDecimal('5');
        $fiveI = new ImmutableDecimal('5i');
        $negFive = new ImmutableDecimal('-5');
        $negFiveI = new ImmutableDecimal('-5i');
        $zero = new ImmutableDecimal('0');
        $zeroI = new ImmutableDecimal('0i');
        $one = new ImmutableDecimal('1');
        $oneI = new ImmutableDecimal('1i');
        $tenPowThirty = new ImmutableDecimal('1000000000000000000000000000000');
        $tenPowThirtyI = new ImmutableDecimal('1000000000000000000000000000000i');
        $tenScale = new ImmutableDecimal('0.0000000001');
        $tenScaleI = new ImmutableDecimal('0.0000000001i');
        $twelveScale = new ImmutableDecimal('0.000000000001');
        $twelveScaleI = new ImmutableDecimal('0.000000000001i');

        $a = new MutableComplexNumber($five, $fiveI);
        $b = new MutableComplexNumber($negFive, $negFiveI);
        $c = new MutableComplexNumber($five, $negFiveI);
        $d = new MutableComplexNumber($negFive, $fiveI);
        $e = new MutableComplexNumber($one, $tenPowThirtyI);
        $f = new MutableComplexNumber($tenPowThirty, $oneI);
        $g = new MutableComplexNumber($one, $tenScaleI);
        $h = new MutableComplexNumber($tenScale, $oneI);
        $i = new MutableComplexNumber($one, $twelveScaleI);
        $j = new MutableComplexNumber($twelveScale, $oneI);
        $k = new MutableComplexNumber($one, $zeroI);
        $l = new MutableComplexNumber($zero, $oneI);

        return [
            'MComplex (5+5i)+(5+5i)' => [$a, $a, '10+10i', MutableComplexNumber::class],
            'MComplex (5+5i)+(-5-5i)' => [$a, $b, '5+5i', MutableComplexNumber::class],
            'MComplex (5+5i)+(5-5i)' => [$a, $c, '10', ImmutableDecimal::class],
            'MComplex (5+5i)+(-5+5i)' => [$a, $d, '10i', ImmutableDecimal::class],
            'MComplex (5+5i)+(5i)' => [$a, $fiveI, '5+10i', MutableComplexNumber::class],
            'MComplex (5i)+(5+5i)' => [$fiveI, $a, '5+15i', ImmutableComplexNumber::class],
            'MComplex (1+1000000000000000000000000000000i)+(5+5i)' => [$e, $a, '6+1000000000000000000000000000010i', MutableComplexNumber::class],
            'MComplex (1000000000000000000000000000000+1i)+(5+5i)' => [$f, $a, '1000000000000000000000000000005+11i', MutableComplexNumber::class],
            'MComplex (1+0.0000000001i)+(5+5i)' => [$g, $a, '6+10.0000000001i', MutableComplexNumber::class],
            'MComplex (0.0000000001+1i)+(5+5i)' => [$h, $a, '5.0000000001+11i', MutableComplexNumber::class],
            'MComplex (1+0.000000000001i)+(5+5i)' => [$i, $a, '6+10.000000000001i', MutableComplexNumber::class],
            'MComplex (0.000000000001+1i)+(5+5i)' => [$j, $a, '5.000000000001+11i', MutableComplexNumber::class],
            'MComplex (1+0i)+(5+5i)' => [$k, $a, '6+10i', MutableComplexNumber::class],
            'MComplex (0+1i)+(5+5i)' => [$l, $a, '5+11i', MutableComplexNumber::class],
        ];
    }

    /**
     * @dataProvider additionImmutableComplexProvider
     * @dataProvider additionMutableComplexProvider
     */
    public function testAdd(NumberInterface $a, NumberInterface $b, string $expected, ?string $resultClass = null)
    {
        if (str_contains($expected, 'Exception')) {
            $this->expectException($expected);
            $a->add($b);
        } else {
            $answer = $a->add($b);
            $this->assertEquals($expected, $answer->getValue());
            if (!is_null($resultClass)) {
                $this->assertEquals($resultClass, get_class($answer));
            }
        }
    }

    /*
     * subtract()
     */

    public function subtractionImmutableComplexProvider(): array
    {
        $five = new ImmutableDecimal('5');
        $fiveI = new ImmutableDecimal('5i');
        $negFive = new ImmutableDecimal('-5');
        $negFiveI = new ImmutableDecimal('-5i');
        $zero = new ImmutableDecimal('0');
        $zeroI = new ImmutableDecimal('0i');
        $one = new ImmutableDecimal('1');
        $oneI = new ImmutableDecimal('1i');
        $tenPowThirty = new ImmutableDecimal('1000000000000000000000000000000');
        $tenPowThirtyI = new ImmutableDecimal('1000000000000000000000000000000i');
        $tenScale = new ImmutableDecimal('0.0000000001');
        $tenScaleI = new ImmutableDecimal('0.0000000001i');
        $twelveScale = new ImmutableDecimal('0.000000000001');
        $twelveScaleI = new ImmutableDecimal('0.000000000001i');

        $a = new ImmutableComplexNumber($five, $fiveI);
        $b = new ImmutableComplexNumber($negFive, $negFiveI);
        $c = new ImmutableComplexNumber($five, $negFiveI);
        $d = new ImmutableComplexNumber($negFive, $fiveI);
        $e = new ImmutableComplexNumber($one, $tenPowThirtyI);
        $f = new ImmutableComplexNumber($tenPowThirty, $oneI);
        $g = new ImmutableComplexNumber($one, $tenScaleI);
        $h = new ImmutableComplexNumber($tenScale, $oneI);
        $i = new ImmutableComplexNumber($one, $twelveScaleI);
        $j = new ImmutableComplexNumber($twelveScale, $oneI);
        $k = new ImmutableComplexNumber($one, $zeroI);
        $l = new ImmutableComplexNumber($zero, $oneI);

        return [
            'IComplex (5+5i)-(5+5i)' => [$a, $a, '0', ImmutableDecimal::class],
            'IComplex (5+5i)-(-5-5i)' => [$a, $b, '10+10i', ImmutableComplexNumber::class],
            'IComplex (5+5i)-(5-5i)' => [$a, $c, '10i', ImmutableDecimal::class],
            'IComplex (5+5i)-(-5+5i)' => [$a, $d, '10', ImmutableDecimal::class],
            'IComplex (5+5i)-(5i)' => [$a, $fiveI, '5', ImmutableDecimal::class],
            'IComplex (5i)-(5+5i)' => [$fiveI, $a, '-5', ImmutableDecimal::class],
            'IComplex (1+1000000000000000000000000000000i)-(5+5i)' => [$e, $a, '-4+999999999999999999999999999995i', ImmutableComplexNumber::class],
            'IComplex (1000000000000000000000000000000+1i)-(5+5i)' => [$f, $a, '999999999999999999999999999995-4i', ImmutableComplexNumber::class],
            'IComplex (1+0.0000000001i)-(5+5i)' => [$g, $a, '-4-4.9999999999i', ImmutableComplexNumber::class],
            'IComplex (0.0000000001+1i)-(5+5i)' => [$h, $a, '-4.9999999999-4i', ImmutableComplexNumber::class],
            'IComplex (1+0.000000000001i)-(5+5i)' => [$i, $a, '-4-4.999999999999i', ImmutableComplexNumber::class],
            'IComplex (0.000000000001+1i)-(5+5i)' => [$j, $a, '-4.999999999999-4i', ImmutableComplexNumber::class],
            'IComplex (1+0i)-(5+5i)' => [$k, $a, '-4-5i', ImmutableComplexNumber::class],
            'IComplex (0+1i)-(5+5i)' => [$l, $a, '-5-4i', ImmutableComplexNumber::class],
        ];
    }

    public function subtractionMutableComplexProvider(): array
    {
        $five = new ImmutableDecimal('5');
        $fiveI = new ImmutableDecimal('5i');
        $negFive = new ImmutableDecimal('-5');
        $negFiveI = new ImmutableDecimal('-5i');
        $zero = new ImmutableDecimal('0');
        $zeroI = new ImmutableDecimal('0i');
        $one = new ImmutableDecimal('1');
        $oneI = new ImmutableDecimal('1i');
        $tenPowThirty = new ImmutableDecimal('1000000000000000000000000000000');
        $tenPowThirtyI = new ImmutableDecimal('1000000000000000000000000000000i');
        $tenScale = new ImmutableDecimal('0.0000000001');
        $tenScaleI = new ImmutableDecimal('0.0000000001i');
        $twelveScale = new ImmutableDecimal('0.000000000001');
        $twelveScaleI = new ImmutableDecimal('0.000000000001i');

        $a = new MutableComplexNumber($five, $fiveI);
        $b = new MutableComplexNumber($negFive, $negFiveI);
        $c = new MutableComplexNumber($five, $negFiveI);
        $d = new MutableComplexNumber($negFive, $fiveI);
        $e = new MutableComplexNumber($one, $tenPowThirtyI);
        $f = new MutableComplexNumber($tenPowThirty, $oneI);
        $g = new MutableComplexNumber($one, $tenScaleI);
        $h = new MutableComplexNumber($tenScale, $oneI);
        $i = new MutableComplexNumber($one, $twelveScaleI);
        $j = new MutableComplexNumber($twelveScale, $oneI);
        $k = new MutableComplexNumber($one, $zeroI);
        $l = new MutableComplexNumber($zero, $oneI);

        return [
            'MComplex (5+5i)-(5+5i)' => [$a, $a, '0', ImmutableDecimal::class],
            'MComplex (5+5i)-(-5-5i)' => [$a, $b, '10+10i', MutableComplexNumber::class],
            'MComplex (5+5i)-(5-5i)' => [$a, $c, '5+15i', MutableComplexNumber::class],
            'MComplex (5+5i)-(-5+5i)' => [$a, $d, '10+10i', MutableComplexNumber::class],
            'MComplex (5+5i)-(5i)' => [$a, $fiveI, '10+5i', MutableComplexNumber::class],
            'MComplex (5i)-(5+5i)' => [$fiveI, $a, '-10', ImmutableDecimal::class],
            'MComplex (1+1000000000000000000000000000000i)-(5+5i)' => [$e, $a, '-9+999999999999999999999999999995i', MutableComplexNumber::class],
            'MComplex (1000000000000000000000000000000+1i)-(5+5i)' => [$f, $a, '999999999999999999999999999990-4i', MutableComplexNumber::class],
            'MComplex (1+0.0000000001i)-(5+5i)' => [$g, $a, '-9-4.9999999999i', MutableComplexNumber::class],
            'MComplex (0.0000000001+1i)-(5+5i)' => [$h, $a, '-9.9999999999-4i', MutableComplexNumber::class],
            'MComplex (1+0.000000000001i)-(5+5i)' => [$i, $a, '-9-4.999999999999i', MutableComplexNumber::class],
            'MComplex (0.000000000001+1i)-(5+5i)' => [$j, $a, '-9.999999999999-4i', MutableComplexNumber::class],
            'MComplex (1+0i)-(5+5i)' => [$k, $a, '-9-5i', MutableComplexNumber::class],
            'MComplex (0+1i)-(5+5i)' => [$l, $a, '-10-4i', MutableComplexNumber::class],
        ];
    }

    /**
     * @dataProvider subtractionImmutableComplexProvider
     * @dataProvider subtractionMutableComplexProvider
     */
    public function testSubtract(NumberInterface $a, NumberInterface $b, string $expected, ?string $resultClass = null)
    {
        if (str_contains($expected, 'Exception')) {
            $this->expectException($expected);
            $a->subtract($b);
        } else {
            $answer = $a->subtract($b);
            $this->assertEquals($expected, $answer->getValue());
            if (!is_null($resultClass)) {
                $this->assertEquals($resultClass, get_class($answer));
            }
        }
    }

    /*
     * multiply()
     */

    public function multiplicationImmutableComplexProvider(): array
    {
        $five = new ImmutableDecimal('5');
        $fiveI = new ImmutableDecimal('5i');
        $negFive = new ImmutableDecimal('-5');
        $negFiveI = new ImmutableDecimal('-5i');
        $zero = new ImmutableDecimal('0');
        $zeroI = new ImmutableDecimal('0i');
        $one = new ImmutableDecimal('1');
        $oneI = new ImmutableDecimal('1i');
        $tenPowThirty = new ImmutableDecimal('1000000000000000000000000000000');
        $tenPowThirtyI = new ImmutableDecimal('1000000000000000000000000000000i');
        $tenScale = new ImmutableDecimal('0.0000000001');
        $tenScaleI = new ImmutableDecimal('0.0000000001i');
        $twelveScale = new ImmutableDecimal('0.000000000001');
        $twelveScaleI = new ImmutableDecimal('0.000000000001i');

        $a = new ImmutableComplexNumber($five, $fiveI);
        $b = new ImmutableComplexNumber($negFive, $negFiveI);
        $c = new ImmutableComplexNumber($five, $negFiveI);
        $d = new ImmutableComplexNumber($negFive, $fiveI);
        $e = new ImmutableComplexNumber($one, $tenPowThirtyI);
        $f = new ImmutableComplexNumber($tenPowThirty, $oneI);
        $g = new ImmutableComplexNumber($one, $tenScaleI);
        $h = new ImmutableComplexNumber($tenScale, $oneI);
        $i = new ImmutableComplexNumber($one, $twelveScaleI);
        $j = new ImmutableComplexNumber($twelveScale, $oneI);
        $k = new ImmutableComplexNumber($one, $zeroI);
        $l = new ImmutableComplexNumber($zero, $oneI);

        return [
            'IComplex (5+5i)*(5+5i)' => [$a, $a, '50i', ImmutableDecimal::class],
            'IComplex (5+5i)*(-5-5i)' => [$a, $b, '-50i', ImmutableDecimal::class],
            'IComplex (5+5i)*(5-5i)' => [$a, $c, '50', ImmutableDecimal::class],
            'IComplex (5+5i)*(-5+5i)' => [$a, $d, '-50', ImmutableDecimal::class],
            'IComplex (5+5i)*(0)' => [$a, $zero, '0', ImmutableDecimal::class],
            'IComplex (5+5i)*(5i)' => [$a, $fiveI, '-25+25i', ImmutableComplexNumber::class],
            'IComplex (5i)*(5+5i)' => [$fiveI, $a, '-25+25i', ImmutableComplexNumber::class],
            'IComplex (1+1000000000000000000000000000000i)*(5+5i)' => [$e, $a, '-4999999999999999999999999999995+5000000000000000000000000000005i', ImmutableComplexNumber::class],
            'IComplex (1000000000000000000000000000000+1i)*(5+5i)' => [$f, $a, '4999999999999999999999999999995+5000000000000000000000000000005i', ImmutableComplexNumber::class],
            'IComplex (1+0.0000000001i)*(5+5i)' => [$g, $a, '4.9999999995+5.0000000005i', ImmutableComplexNumber::class],
            'IComplex (0.0000000001+1i)*(5+5i)' => [$h, $a, '-4.9999999995+5.0000000005i', ImmutableComplexNumber::class],
            'IComplex (1+0.000000000001i)*(5+5i)' => [$i, $a, '5+5i', ImmutableComplexNumber::class],
            'IComplex (0.000000000001+1i)*(5+5i)' => [$j, $a, '-5+5i', ImmutableComplexNumber::class],
            'IComplex (1+0i)*(5+5i)' => [$k, $a, '5+5i', ImmutableComplexNumber::class],
            'IComplex (0+1i)*(5+5i)' => [$l, $a, '-5+5i', ImmutableComplexNumber::class],
        ];
    }

    public function multiplicationMutableComplexProvider(): array
    {
        $five = new ImmutableDecimal('5');
        $fiveI = new ImmutableDecimal('5i');
        $negFive = new ImmutableDecimal('-5');
        $negFiveI = new ImmutableDecimal('-5i');
        $zero = new ImmutableDecimal('0');
        $zeroI = new ImmutableDecimal('0i');
        $one = new ImmutableDecimal('1');
        $oneI = new ImmutableDecimal('1i');
        $tenPowThirty = new ImmutableDecimal('1000000000000000000000000000000');
        $tenPowThirtyI = new ImmutableDecimal('1000000000000000000000000000000i');
        $tenScale = new ImmutableDecimal('0.0000000001');
        $tenScaleI = new ImmutableDecimal('0.0000000001i');
        $twelveScale = new ImmutableDecimal('0.000000000001');
        $twelveScaleI = new ImmutableDecimal('0.000000000001i');

        $a = new MutableComplexNumber($five, $fiveI);
        $b = new MutableComplexNumber($negFive, $negFiveI);
        $c = new MutableComplexNumber($five, $negFiveI);
        $d = new MutableComplexNumber($negFive, $fiveI);
        $e = new MutableComplexNumber($one, $tenPowThirtyI);
        $f = new MutableComplexNumber($tenPowThirty, $oneI);
        $g = new MutableComplexNumber($one, $tenScaleI);
        $h = new MutableComplexNumber($tenScale, $oneI);
        $i = new MutableComplexNumber($one, $twelveScaleI);
        $j = new MutableComplexNumber($twelveScale, $oneI);
        $k = new MutableComplexNumber($one, $zeroI);
        $l = new MutableComplexNumber($zero, $oneI);

        return [
            'MComplex (5+5i)*(5+5i)' => [$a, $a, '50i', ImmutableDecimal::class],
            'MComplex (5+5i)*(-5-5i)' => [$a, $b, '-50i', ImmutableDecimal::class],
            'MComplex (5+5i)*(5-5i)' => [$a, $c, '50', ImmutableDecimal::class],
            'MComplex (5+5i)*(-5+5i)' => [$a, $d, '-50', ImmutableDecimal::class],
            'MComplex (5+5i)*(0)' => [$a, $zero, '0', ImmutableDecimal::class],
            'MComplex (5+5i)*(5i)' => [$a, $fiveI, '-25+25i', MutableComplexNumber::class],
            'MComplex (5i)*(5+5i)' => [$fiveI, $a, '-125-125i', ImmutableComplexNumber::class],
            'MComplex (1+1000000000000000000000000000000i)*(5+5i)' => [$e, $a, '-25000000000000000000000000000025-24999999999999999999999999999975i', MutableComplexNumber::class],
            'MComplex (1000000000000000000000000000000+1i)*(5+5i)' => [$f, $a, '-25000000000000000000000000000025+24999999999999999999999999999975i', MutableComplexNumber::class],
            'MComplex (1+0.0000000001i)*(5+5i)' => [$g, $a, '-25.0000000025+24.9999999975i', MutableComplexNumber::class],
            'MComplex (0.0000000001+1i)*(5+5i)' => [$h, $a, '-25.0000000025-24.9999999975i', MutableComplexNumber::class],
            'MComplex (1+0.000000000001i)*(5+5i)' => [$i, $a, '-25+25i', MutableComplexNumber::class],
            'MComplex (0.000000000001+1i)*(5+5i)' => [$j, $a, '-25-25i', MutableComplexNumber::class],
            'MComplex (1+0i)*(5+5i)' => [$k, $a, '-25+25i', MutableComplexNumber::class],
            'MComplex (0+1i)*(5+5i)' => [$l, $a, '-25-25i', MutableComplexNumber::class],
        ];
    }

    /**
     * @medium
     * @dataProvider multiplicationImmutableComplexProvider
     * @dataProvider multiplicationMutableComplexProvider
     */
    public function testMultiply(NumberInterface $a, NumberInterface $b, string $expected, ?string $resultClass = null)
    {
        if (str_contains($expected, 'Exception')) {
            $this->expectException($expected);
            $a->multiply($b);
        } else {
            $answer = $a->multiply($b);
            $this->assertEquals($expected, $answer->getValue());
            if (!is_null($resultClass)) {
                $this->assertEquals($resultClass, get_class($answer));
            }
        }
    }

    /*
     * divide()
     */

    public function divisionImmutableComplexProvider(): array
    {
        $five = new ImmutableDecimal('5');
        $fiveI = new ImmutableDecimal('5i');
        $negFive = new ImmutableDecimal('-5');
        $negFiveI = new ImmutableDecimal('-5i');
        $zero = new ImmutableDecimal('0');
        $zeroI = new ImmutableDecimal('0i');
        $one = new ImmutableDecimal('1');
        $oneI = new ImmutableDecimal('1i');
        $tenPowThirty = new ImmutableDecimal('1000000000000000000000000000000');
        $tenPowThirtyI = new ImmutableDecimal('1000000000000000000000000000000i');
        $tenScale = new ImmutableDecimal('0.0000000001');
        $tenScaleI = new ImmutableDecimal('0.0000000001i');
        $twelveScale = new ImmutableDecimal('0.000000000001');
        $twelveScaleI = new ImmutableDecimal('0.000000000001i');

        $a = new ImmutableComplexNumber($five, $fiveI);
        $b = new ImmutableComplexNumber($negFive, $negFiveI);
        $c = new ImmutableComplexNumber($five, $negFiveI);
        $d = new ImmutableComplexNumber($negFive, $fiveI);
        $e = new ImmutableComplexNumber($one, $tenPowThirtyI);
        $f = new ImmutableComplexNumber($tenPowThirty, $oneI);
        $g = new ImmutableComplexNumber($one, $tenScaleI);
        $h = new ImmutableComplexNumber($tenScale, $oneI);
        $i = new ImmutableComplexNumber($one, $twelveScaleI);
        $j = new ImmutableComplexNumber($twelveScale, $oneI);
        $k = new ImmutableComplexNumber($one, $zeroI);
        $l = new ImmutableComplexNumber($zero, $oneI);

        return [
            'IComplex (5+5i)/(5+5i)' => [$a, $a, '1', ImmutableDecimal::class],
            'IComplex (5+5i)/(-5-5i)' => [$a, $b, '-1', ImmutableDecimal::class],
            'IComplex (5+5i)/(5-5i)' => [$a, $c, '1i', ImmutableDecimal::class],
            'IComplex (5+5i)/(-5+5i)' => [$a, $d, '-1i', ImmutableDecimal::class],
            'IComplex (5+5i)/(0)' => [$a, $zero, IntegrityConstraint::class, null],
            'IComplex (5+5i)/(5i)' => [$a, $fiveI, '1-1i', ImmutableComplexNumber::class],
            'IComplex (5i)/(5+5i)' => [$fiveI, $a, '0.5+0.5i', ImmutableComplexNumber::class],
            'IComplex (1+1000000000000000000000000000000i)/(5+5i)' => [$e, $a, '100000000000000000000000000000.1+99999999999999999999999999999.9i', ImmutableComplexNumber::class],
            'IComplex (1000000000000000000000000000000+1i)/(5+5i)' => [$f, $a, '100000000000000000000000000000.1-99999999999999999999999999999.9i', ImmutableComplexNumber::class],
            'IComplex (1+0.0000000001i)/(5+5i)' => [$g, $a, '0.1-0.1i', ImmutableComplexNumber::class],
            'IComplex (0.0000000001+1i)/(5+5i)' => [$h, $a, '0.1+0.1i', ImmutableComplexNumber::class],
            'IComplex (1+0.000000000001i)/(5+5i)' => [$i, $a, '0.1-0.1i', ImmutableComplexNumber::class],
            'IComplex (0.000000000001+1i)/(5+5i)' => [$j, $a, '0.1+0.1i', ImmutableComplexNumber::class],
            'IComplex (1+0i)/(5+5i)' => [$k, $a, '0.1-0.1i', ImmutableComplexNumber::class],
            'IComplex (0+1i)/(5+5i)' => [$l, $a, '0.1+0.1i', ImmutableComplexNumber::class],
        ];
    }

    public function divisionMutableComplexProvider(): array
    {
        $five = new ImmutableDecimal('5');
        $fiveI = new ImmutableDecimal('5i');
        $negFive = new ImmutableDecimal('-5');
        $negFiveI = new ImmutableDecimal('-5i');
        $zero = new ImmutableDecimal('0');
        $zeroI = new ImmutableDecimal('0i');
        $one = new ImmutableDecimal('1');
        $oneI = new ImmutableDecimal('1i');
        $tenPowThirty = new ImmutableDecimal('1000000000000000000000000000000');
        $tenPowThirtyI = new ImmutableDecimal('1000000000000000000000000000000i');
        $tenScale = new ImmutableDecimal('0.0000000001');
        $tenScaleI = new ImmutableDecimal('0.0000000001i');
        $twelveScale = new ImmutableDecimal('0.000000000001');
        $twelveScaleI = new ImmutableDecimal('0.000000000001i');

        $a = new MutableComplexNumber($five, $fiveI);
        $b = new MutableComplexNumber($negFive, $negFiveI);
        $c = new MutableComplexNumber($five, $negFiveI);
        $d = new MutableComplexNumber($negFive, $fiveI);
        $e = new MutableComplexNumber($one, $tenPowThirtyI);
        $f = new MutableComplexNumber($tenPowThirty, $oneI);
        $g = new MutableComplexNumber($one, $tenScaleI);
        $h = new MutableComplexNumber($tenScale, $oneI);
        $i = new MutableComplexNumber($one, $twelveScaleI);
        $j = new MutableComplexNumber($twelveScale, $oneI);
        $k = new MutableComplexNumber($one, $zeroI);
        $l = new MutableComplexNumber($zero, $oneI);

        return [
            'MComplex (5+5i)/(5+5i)' => [$a, $a, '1', ImmutableDecimal::class],
            'MComplex (5+5i)/(-5-5i)' => [$a, $b, '-1', ImmutableDecimal::class],
            'MComplex (5+5i)/(5-5i)' => [$a, $c, '1i', ImmutableDecimal::class],
            'MComplex (5+5i)/(-5+5i)' => [$a, $d, '-1i', ImmutableDecimal::class],
            'MComplex (5+5i)/(0)' => [$a, $zero, IntegrityConstraint::class, null],
            'MComplex (5+5i)/(5i)' => [$a, $fiveI, '1-1i', MutableComplexNumber::class],
            'MComplex (5i)/(5+5i)' => [$fiveI, $a, '-2.5+2.5i', ImmutableComplexNumber::class],
            'MComplex (1+1000000000000000000000000000000i)/(5+5i)' => [$e, $a, '-499999999999999999999999999999.5+500000000000000000000000000000.5i', MutableComplexNumber::class],
            'MComplex (1000000000000000000000000000000+1i)/(5+5i)' => [$f, $a, '499999999999999999999999999999.5+500000000000000000000000000000.5i', MutableComplexNumber::class],
            'MComplex (1+0.0000000001i)/(5+5i)' => [$g, $a, '0.5+0.5i', MutableComplexNumber::class],
            'MComplex (0.0000000001+1i)/(5+5i)' => [$h, $a, '-0.5+0.5i', MutableComplexNumber::class],
            'MComplex (1+0.000000000001i)/(5+5i)' => [$i, $a, '0.5+0.5i', MutableComplexNumber::class],
            'MComplex (0.000000000001+1i)/(5+5i)' => [$j, $a, '-0.5+0.5i', MutableComplexNumber::class],
            'MComplex (1+0i)/(5+5i)' => [$k, $a, '0.5+0.5i', MutableComplexNumber::class],
            'MComplex (0+1i)/(5+5i)' => [$l, $a, '-0.5+0.5i', MutableComplexNumber::class],
        ];
    }

    /**
     * @medium
     * @dataProvider divisionImmutableComplexProvider
     * @dataProvider divisionMutableComplexProvider
     */
    public function testDivide(NumberInterface $a, NumberInterface $b, string $expected, ?string $resultClass = null)
    {
        if (str_contains($expected, 'Exception')) {
            $this->expectException($expected);
            $a->divide($b);
        } else {
            $answer = $a->divide($b);
            $this->assertEquals($expected, $answer->getValue());
            if (!is_null($resultClass)) {
                $this->assertEquals($resultClass, get_class($answer));
            }
        }
    }

}