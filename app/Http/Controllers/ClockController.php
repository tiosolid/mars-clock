<?php

namespace App\Http\Controllers;

use App\Models\MarsTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Exception;

class ClockController extends Controller
{
    public const MSD_FIELD_NAME = 'marsSolDate';
    public const MTC_FIELD_NAME = 'marsCoordinatedTime';

    /**
     * Receives and process requests for the POST verb
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function post(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $response = new Response();

        try {
            $this->validateRequest($request);

            $utcDateTime = $request->input(MarsTime::UTC_TIME_FIELD_NAME);
            $marsTime = new MarsTime($utcDateTime);

            $responseContent = [
                self::MSD_FIELD_NAME => $marsTime->getMarsSolDate(),
                self::MTC_FIELD_NAME => $marsTime->getFormattedMtcTime(),
                //TODO: Hide this based on env
//                'debug' => [
//                    'utcTime' => $marsTime->getUtcDateTime(),
//                    'julianDate' => $marsTime->getJulianDate(),
//                    'julianTerrestrial' => $marsTime->getJulianDateTerrestrial(),
//                    'daysSinceJ2000Epoch' => $marsTime->getDaysSinceJ200Epoch(),
//                    'mtcHours' => $marsTime->getMtcTime(),
//                ],
            ];

            $response->setContent($responseContent);
            $response->setStatusCode(200);
        } catch (ValidationException $e) {
            //TODO: Create a class for building error responses
            $response->setContent([
                'error' => [
                    'message' => $e->getMessage(),
                    'details' => $e->errors()
                ]
            ]);
            $response->setStatusCode(422);
        } catch (Exception $e) {
            //TODO: Create a class for building error responses
            $response->setContent([
                'error' => [
                    'message' => $e->getMessage(),
                    'details' => null
                ]
            ]);
            $response->setStatusCode(500);
        }

        return $response;
    }

    /**
     * @throws ValidationException
     */
    private function validateRequest(Request $request)
    {
        /** @link https://laravel.com/docs/8.x/validation */
        $this->validate($request, [
            //TODO: Set custom messages for validation
            //TODO: Add better validation to old dates (which cause the API to return negative values)
            //TODO: Add stricter validation (so that only full UTC Date/Time Strings are accepted and nothing else)
            MarsTime::UTC_TIME_FIELD_NAME => ['required']
        ]);
    }
}
