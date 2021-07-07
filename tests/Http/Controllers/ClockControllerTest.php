<?php

namespace Http\Controllers;

use App\Http\Controllers\ClockController;
use App\Models\MarsTime;
use TestCase;

/** @covers \App\Http\Controllers\ClockController */
class ClockControllerTest extends TestCase
{
    /**
     * @dataProvider validParametersProvider
     */
    public function testValidParameters(string $utcDateTimeString, float $expectedMsdValue, string $expectedMtcValue)
    {
        $this->json('POST', '/clock', [MarsTime::UTC_TIME_FIELD_NAME => $utcDateTimeString])
            ->seeJson([
                ClockController::MSD_FIELD_NAME => $expectedMsdValue,
                ClockController::MTC_FIELD_NAME => $expectedMtcValue
            ]);
    }

    /** @dataProvider invalidParametersProvider */
    public function testInvalidParameters(string $utcDateTimeString)
    {
        $this->json('POST', '/clock', [MarsTime::UTC_TIME_FIELD_NAME => $utcDateTimeString])
            ->seeJson([
                "utcDateTime" => ["The utc date time is not a valid date."]
            ]);
    }

    public function testEmptyParameters()
    {
        $this->json('POST', '/clock')
            ->seeJson([
                "utcDateTime" => ["The utc date time field is required."]
            ]);
    }

    public function testUnsupportedVerbs()
    {
        $this->json('GET', '/clock')->seeStatusCode(405);
        $this->json('PUT', '/clock')->seeStatusCode(405);
        $this->json('PATCH', '/clock')->seeStatusCode(405);
        $this->json('DELETE', '/clock')->seeStatusCode(405);
        $this->json('OPTIONS', '/clock')->seeStatusCode(405);
    }

    /**
     * @see ClockController::validateRequest()
     * @see MarsTime::__construct()
     */
    public function validParametersProvider(): array
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
    public function invalidParametersProvider(): array
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
