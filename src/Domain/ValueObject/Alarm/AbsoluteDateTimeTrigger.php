<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) 2025 Markus Poerschke <markus@poerschke.nrw>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LordSimal\iCal\Domain\ValueObject\Alarm;

use LordSimal\iCal\Domain\ValueObject\Timestamp;

final class AbsoluteDateTimeTrigger extends Trigger
{
    private Timestamp $dateTime;

    public function __construct(Timestamp $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function getDateTime(): Timestamp
    {
        return $this->dateTime;
    }
}
