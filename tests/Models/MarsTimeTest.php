<?php

namespace Models;

use App\Models\MarsTime;
use Illuminate\Validation\ValidationException;
use TestCase;

class MarsTimeTest extends TestCase
{
    /** @dataProvider validDateTimeStringsProvider */
    public function testCanBeCreated(string $utcDateTimeString)
    {
        $this->assertInstanceOf(
            MarsTime::class,
            new MarsTime($utcDateTimeString)
        );
    }

    /** @dataProvider invalidDateTimeStringsProvider */
    public function testCannotBeCreated(string $utcDateTimeString)
    {
        $this->expectException(ValidationException::class);

        new MarsTime($utcDateTimeString);
    }

    /** @dataProvider validDateTimeStringsProvider */
    public function testValidClockValues(string $utcDateTimeString, float $expectedMsdValue, string $expectedMtcValue)
    {
        $marsTime = new MarsTime($utcDateTimeString);

        $this->assertEquals($expectedMsdValue, $marsTime->getMarsSolDate());
        $this->assertEquals($expectedMtcValue, $marsTime->getFormattedMtcTime());
    }

    /**
     * @see ClockController::validateRequest()
     * @see MarsTime::__construct()
     */
    public function validDateTimeStringsProvider(): array
    {
        return [
            //UTC Date/Time string, Expected MSD, Expected MTC
            'Full Date/Time String' => ['Wed, 23 Jun 2021 20:22:31 GMT', 52426.088109262855, "02:06:52"],
            'Full Date String' => ['12.08.2021', 52473.84296524192, "20:13:52"],
            'Unix Timestamp' => ['@1215282385', 47816.724656502345, "17:23:30"],
        ];
    }

    /**
     * @see ClockController::validateRequest()
     * @see MarsTime::__construct()
     */
    public function invalidDateTimeStringsProvider(): array
    {
        return [
            //UTC Date/Time string
            'Invalid Timezone' => ['Wed, 23 Jun 2021 20:22:31 GTT'],
            'Invalid Date String' => ['20/10/2021'],
            'Invalid Time String' => ['22:30:00'],
            'Valid Date/Time String, Invalid Month' => ['Wed, 23 Sus 2021 20:22:31 GMT'],
            'Random String' => ['aaaaaaaaaaa'],
        ];
    }
}
