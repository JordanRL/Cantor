<?php

namespace Samsara\Fermat\Types\Traits;

use Samsara\Exceptions\SystemError\PlatformError\MissingPackage;
use Samsara\Exceptions\UsageError\IntegrityConstraint;
use Samsara\Fermat\Types\Base\Interfaces\Numbers\DecimalInterface;
use Samsara\Fermat\Types\Traits\Decimal\LogNativeTrait;
use Samsara\Fermat\Types\Traits\Decimal\LogScaleTrait;
use Samsara\Fermat\Types\Traits\Decimal\LogSelectionTrait;

/**
 *
 */
trait LogSimpleTrait
{
    use LogNativeTrait;
    use LogScaleTrait;
    use LogSelectionTrait;

    /**
     * @param int|null $scale
     * @param bool $round
     * @return DecimalInterface
     * @throws IntegrityConstraint
     * @throws MissingPackage
     */
    public function exp(?int $scale = null, bool $round = true): DecimalInterface
    {
        $answer = $this->expSelector($scale);

        $finalScale = $scale ?? $this->getScale();

        if ($round) {
            $result = $this->setValue($answer)->roundToScale($finalScale);
        } else {
            $result = $this->setValue($answer)->truncateToScale($finalScale);
        }

        return $result;
    }

    /**
     * @param int|null $scale
     * @param bool $round
     * @return DecimalInterface
     * @throws IntegrityConstraint
     * @throws MissingPackage
     */
    public function ln(?int $scale = null, bool $round = true): DecimalInterface
    {
        $answer = $this->lnSelector($scale);

        $finalScale = $scale ?? $this->getScale();

        if ($round) {
            $result = $this->setValue($answer)->roundToScale($finalScale);
        } else {
            $result = $this->setValue($answer)->truncateToScale($finalScale);
        }

        return $result;
    }

    /**
     * @param int|null $scale
     * @param bool $round
     * @return DecimalInterface
     * @throws IntegrityConstraint
     */
    public function log10(?int $scale = null, bool $round = true): DecimalInterface
    {
        $answer = $this->log10Selector($scale);

        $finalScale = $scale ?? $this->getScale();

        if ($round) {
            $result = $this->setValue($answer)->roundToScale($finalScale);
        } else {
            $result = $this->setValue($answer)->truncateToScale($finalScale);
        }

        return $result;
    }

}