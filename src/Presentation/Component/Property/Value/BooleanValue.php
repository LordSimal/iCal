<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) 2025 Markus Poerschke <markus@poerschke.nrw>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LordSimal\iCal\Presentation\Component\Property\Value;

use LordSimal\iCal\Presentation\Component\Property\Value;

final class BooleanValue extends Value
{
    private string $valueAsString;

    public function __construct(bool $value)
    {
        $this->valueAsString = $value ? 'TRUE' : 'FALSE';
    }

    public function __toString(): string
    {
        return $this->valueAsString;
    }
}
