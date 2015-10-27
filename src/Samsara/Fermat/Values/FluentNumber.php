<?php

namespace Samsara\Fermat\Values;

use Samsara\Fermat\Types\Value;
use Samsara\Fermat\Types\NumberInterface;

class FluentNumber extends Value implements NumberInterface
{

    public function modulo($mod)
    {
        $oldBase = $this->convertForModification();

        return (new FluentNumber(bcmod($this->getValue(), $mod), $this->getPrecision()))->convertFromModification($oldBase);
    }

    protected function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

}