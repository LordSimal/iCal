<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) 2025 Markus Poerschke <markus@poerschke.nrw>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LordSimal\iCal\Presentation\Factory;

use LordSimal\iCal\Domain\ValueObject\Alarm;
use LordSimal\iCal\Domain\ValueObject\Alarm\AbsoluteDateTimeTrigger;
use LordSimal\iCal\Domain\ValueObject\Alarm\Action;
use LordSimal\iCal\Domain\ValueObject\Alarm\AudioAction;
use LordSimal\iCal\Domain\ValueObject\Alarm\DisplayAction;
use LordSimal\iCal\Domain\ValueObject\Alarm\EmailAction;
use LordSimal\iCal\Domain\ValueObject\Alarm\RelativeTrigger;
use LordSimal\iCal\Domain\ValueObject\Alarm\Trigger;
use LordSimal\iCal\Presentation\Component;
use LordSimal\iCal\Presentation\Component\Property;
use LordSimal\iCal\Presentation\Component\Property\Parameter;
use LordSimal\iCal\Presentation\Component\Property\Value\DateTimeValue;
use LordSimal\iCal\Presentation\Component\Property\Value\DurationValue;
use LordSimal\iCal\Presentation\Component\Property\Value\IntegerValue;
use LordSimal\iCal\Presentation\Component\Property\Value\TextValue;
use Generator;

/**
 * @SuppressWarnings("CouplingBetweenObjects")
 */
class AlarmFactory
{
    public function createComponent(Alarm $alarm): Component
    {
        return new Component('VALARM', iterator_to_array($this->getProperties($alarm), false));
    }

    /**
     * @return Generator<Property>
     */
    private function getProperties(Alarm $alarm): Generator
    {
        yield from $this->getActionProperties($alarm->getAction());
        yield from $this->getTriggerProperties($alarm->getTrigger());
        yield from $this->getRepeatProperties($alarm);
    }

    /**
     * @return Generator<Property>
     */
    private function getRepeatProperties(Alarm $alarm): Generator
    {
        if (!$alarm->isRepeated()) {
            return;
        }

        yield new Property('REPEAT', new IntegerValue($alarm->getRepeatCount()));
        yield new Property('DURATION', new DurationValue($alarm->getRepeatInterval()));
    }

    /**
     * @return Generator<Property>
     */
    private function getTriggerProperties(Trigger $trigger): Generator
    {
        if ($trigger instanceof AbsoluteDateTimeTrigger) {
            yield new Property(
                'TRIGGER',
                new DateTimeValue($trigger->getDateTime()),
                [
                    new Parameter('VALUE', new TextValue('DATE-TIME')),
                ]
            );
        }

        if ($trigger instanceof RelativeTrigger) {
            yield new Property(
                'TRIGGER',
                new DurationValue($trigger->getDuration()),
                $trigger->isRelatedToEnd() ? [new Parameter('RELATED', new TextValue('END'))] : []
            );
        }
    }

    /**
     * @return Generator<Property>
     */
    private function getActionProperties(Action $action): Generator
    {
        if ($action instanceof AudioAction) {
            yield new Property('ACTION', new TextValue('AUDIO'));
        }

        if ($action instanceof EmailAction) {
            yield new Property('ACTION', new TextValue('EMAIL'));
            yield new Property('SUMMARY', new TextValue($action->getSummary()));
            yield new Property('DESCRIPTION', new TextValue($action->getDescription()));
        }

        if ($action instanceof DisplayAction) {
            yield new Property('ACTION', new TextValue('DISPLAY'));
            yield new Property('DESCRIPTION', new TextValue($action->getDescription()));
        }
    }
}
