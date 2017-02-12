<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Models\Combination;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class PlanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Plan::latest('created_at')->get();
        foreach ($data as $key => $value) {
            $data[$key]['is_applied'] = $this->returnIsApplied($value['id'], $request->user()->id);
            $data[$key]['creator_image'] = User::find($data[$key]['creator_id'])->image_data;
        }


        return response()->json(
            $data,
            200,
            ['Content-Type' => 'application/json; charset=UTF-8', 'charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function firstIndex(Request $request, $num)
    {
        $data = Plan::latest('id')->take($num)->get();
        foreach ($data as $key => $value) {
            $data[$key]['is_applied'] = $this->returnIsApplied($value['id'], $request->user()->id);
            $data[$key]['creator_image'] = User::find($data[$key]['creator_id'])->image_data;
        }

        return response()->json(
            $data,
            200,
            ['Content-Type' => 'application/json; charset=UTF-8', 'charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function showPlansUnderParam(Request $request, $id, $num)
    {
        $data = Plan::where('id', '<', $id)->latest('id')->take($num)->get();
        foreach ($data as $key => $value) {
            $data[$key]['is_applied'] = $this->returnIsApplied($value['id'], $request->user()->id);
            $data[$key]['creator_image'] = User::find($data[$key]['creator_id'])->image_data;
        }

        return response()->json(
            $data,
            200,
            ['Content-Type' => 'application/json; charset=UTF-8', 'charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            // 'name' => 'required|max:255',
        ]);

        $data = $request->toArray();
        $data['creator_id'] = $request->user()->id;

        try {
            if (Plan::create($data)) {

                return response()->json([
                    'status' => 'true',
                    'data' => ['message' => 'Successful']
                ], 200);
            }
        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }

        return response()->json([
            'status' => 'false'
        ], 404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $data = Plan::find($id);
        $data['is_applied'] = $this->returnIsApplied($id, $request->user()->id);
        $data['creator_image'] = User::find($data['creator_id'])->image_data;

        return response()->json(
            $data,
            200,
            ['Content-Type' => 'application/json; charset=UTF-8', 'charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function showForCreator($creator_id)
    {
        $creator_id = $request->user()->id;
        $data = Plan::where('creator_id', $creator_id)->get();

        return response()->json(
            $data,
            200,
            ['Content-Type' => 'application/json; charset=UTF-8', 'charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function showForParticipant($user_id)
    {
        $data = Plan::where('participant_id', $participant_id)->get();

        return response()->json(
            $data,
            200,
            ['Content-Type' => 'application/json; charset=UTF-8', 'charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function showApplicantsForPlan($id)
    {
        $applicants = Plan::find($id)->users()->get();

        return response()->json(
            $applicants,
            200,
            ['Content-Type' => 'application/json; charset=UTF-8', 'charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            if (Plan::find($id)->update($request->toArray())) {

                return response()->json([
                    'status' => 'true',
                    'data' => ['message' => 'Successful']
                ], 200);
            }

        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }

        return response()->json([
            'status' => 'false'
        ], 404);
    }

    public function updateForUser(Request $request)
    {
        $user_id = $request->user()->id;
        try {
            if (Plan::where('user_id', $user_id)->update($request->toArray())) {

                return response()->json([
                    'status' => 'true',
                    'data' => ['message' => 'Successful']
                ], 200);
            }

        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }

        return response()->json([
            'status' => 'false'
        ], 404);
    }

    // public function updateForParticipant(Request $request)
    // {
    //     try {
    //         if (Plan::where('participant_id', $participant_id)->update($request->toArray())) {
    //
    //             return json_encode([
    //                 'status' => 'true',
    //                 'data' => ['message' => 'Successful']
    //             ]);
    //         }
    //
    //     } catch (Exception $e) {
    //         \Log::info($e->getMessage());
    //     }
    //
    //     return response()->json([
    //         'status' => 'false'
    //     ], 404);
    // }

    public function applyForPlan(Request $request, $id)
    {
        try {
            $user_id = $request->user()->id;
            $plan = Plan::find($id);
            $plan->users()->attach($user_id);

            return response()->json([
                'status' => 'true',
                'data' => ['message' => 'Successful']
            ], 200);
        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }

        return response()->json([
            'status' => 'false'
        ], 404);
    }

    public function acceptApplicationForPlan(Request $request, $id)
    {
        try {
            $data = $request->toArray();
            $plan = Plan::where('id', $id);
            if ($plan->update([
                'participant_id' => $data['participant_id'],
                'is_closed' => true
            ])) {
                $creator_id = $request->user()->id;
                $creatorArray = User::find($creator_id)->toArray();
                $participantArray = User::find($data['participant_id'])->toArray();
                $participantFcmToken = $participantArray['fcm_token'];
                $combination = Combination::create([
                    'creator_id' => $creator_id,
                    'participant_id' => $data['participant_id']
                ]);
                $pushData = [
                    'room_id' => $combination['id'],
                    'creator' => $creatorArray
                ];
                dd($participantFcmToken);
                $this->sendFcm($participantFcmToken, $pushData);

                return response()->json([
                    'status' => 'true',
                    'data' => ['room_id' => $combination['id'], 'participant' => $participantArray]
                ], 200);
            }

        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }

        return response()->json([
            'status' => 'false'
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            if (Plan::find($id)->delete()) {

                return response()->json([
                    'status' => 'true',
                    'data' => ['message' => 'destroy param id = ' . $id]
                ], 200);
            }

        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }

        return response()->json([
            'status' => 'false'
        ], 404);
    }

    public function returnIsApplied($id, $userId) {
        $applicants = Plan::find($id)->users()->get();

        foreach ($applicants as $applicant) {
            if ($applicant['id'] == $userId) {
                return true;
            }
        }

        return false;
    }

    public function sendFcm($fcmToken, $pushData) {
        $optionBuiler = new OptionsBuilder();
        $optionBuiler->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')
                          ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($pushData);

        $option = $optionBuiler->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($fcmToken, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        //return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

        //return Array (key : oldToken, value : new token - you must change the token in your database )
        $downstreamResponse->tokensToModify();

        //return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:errror) - in production you should remove from your database the tokens
    }
}
