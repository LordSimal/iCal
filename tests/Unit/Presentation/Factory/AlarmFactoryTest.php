<?php

/*
 * This file is part of the eluceo/iCal package.
 *
 * (c) 2025 Markus Poerschke <markus@poerschke.nrw>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LordSimal\iCal\Test\Unit\Presentation\Factory;

use DateInterval;
use DateTimeImmutable as PhpDateTimeImmutable;
use LordSimal\iCal\Domain\ValueObject\Alarm;
use LordSimal\iCal\Domain\ValueObject\Alarm\AbsoluteDateTimeTrigger;
use LordSimal\iCal\Domain\ValueObject\Alarm\AudioAction;
use LordSimal\iCal\Domain\ValueObject\Alarm\EmailAction;
use LordSimal\iCal\Domain\ValueObject\DateTime;
use LordSimal\iCal\Presentation\ContentLine;
use LordSimal\iCal\Presentation\Factory\AlarmFactory;
use PHPUnit\Framework\TestCase;

class AlarmFactoryTest extends TestCase
{
    public function testAudioAlarm()
    {
        $alarm = new Alarm(
            new AudioAction(),
            new AbsoluteDateTimeTrigger(new DateTime(new PhpDateTimeImmutable('2020-09-30 00:00:00'), false))
        );

        $expected = implode(ContentLine::LINE_SEPARATOR, [
            'BEGIN:VALARM',
            'ACTION:AUDIO',
            'TRIGGER;VALUE=DATE-TIME:20200930T000000',
            'END:VALARM',
        ]);

        $actual = (string) (new AlarmFactory())->createComponent($alarm);

        self::assertSame(
            $expected,
            trim($actual)
        );
    }

    public function testEmailAlarm()
    {
        $alarm = new Alarm(
            new EmailAction('Summary Text', 'Description Text'),
            new AbsoluteDateTimeTrigger(new DateTime(new PhpDateTimeImmutable('2020-09-30 00:00:00'), false))
        );

        $expected = implode(ContentLine::LINE_SEPARATOR, [
            'BEGIN:VALARM',
            'ACTION:EMAIL',
            'SUMMARY:Summary Text',
            'DESCRIPTION:Description Text',
            'TRIGGER;VALUE=DATE-TIME:20200930T000000',
            'END:VALARM',
        ]);

        $actual = (string) (new AlarmFactory())->createComponent($alarm);

        self::assertSame(
            $expected,
            trim($actual)
        );
    }

    public function testDisplayAlarm()
    {
        $alarm = new Alarm(
            new Alarm\DisplayAction('Description Text'),
            new AbsoluteDateTimeTrigger(new DateTime(new PhpDateTimeImmutable('2020-09-30 00:00:00'), false))
        );

        $expected = implode(ContentLine::LINE_SEPARATOR, [
            'BEGIN:VALARM',
            'ACTION:DISPLAY',
            'DESCRIPTION:Description Text',
            'TRIGGER;VALUE=DATE-TIME:20200930T000000',
            'END:VALARM',
        ]);

        $actual = (string) (new AlarmFactory())->createComponent($alarm);

        self::assertSame(
            $expected,
            trim($actual)
        );
    }

    public function testRelativeTrigger()
    {
        $alarm = new Alarm(
            new AudioAction(),
            new Alarm\RelativeTrigger(new DateInterval('P1D'))
        );

        $expected = implode(ContentLine::LINE_SEPARATOR, [
            'BEGIN:VALARM',
            'ACTION:AUDIO',
            'TRIGGER:P1D',
            'END:VALARM',
        ]);

        $actual = (string) (new AlarmFactory())->createComponent($alarm);

        self::assertSame(
            $expected,
            trim($actual)
        );
    }

    public function testRepeat()
    {
        $alarm = (new Alarm(
            new AudioAction(),
            new AbsoluteDateTimeTrigger(new DateTime(new PhpDateTimeImmutable('2020-09-30 00:00:00'), false))
        ))->withRepeat(3, new DateInterval('P1D'));

        $expected = implode(ContentLine::LINE_SEPARATOR, [
            'BEGIN:VALARM',
            'ACTION:AUDIO',
            'TRIGGER;VALUE=DATE-TIME:20200930T000000',
            'REPEAT:3',
            'DURATION:P1D',
            'END:VALARM',
        ]);

        $actual = (string) (new AlarmFactory())->createComponent($alarm);

        self::assertSame(
            $expected,
            trim($actual)
        );
    }
}
