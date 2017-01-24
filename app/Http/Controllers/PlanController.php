<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
    public function index()
    {
        $data = Plan::latest('created_at')->get();

        return response()->json(
            $data,
            200,
            ['Content-Type' => 'application/json; charset=UTF-8', 'charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
        $data['user_id'] = $request->user()->id;

        try {
            if (Plan::create($data)) {

                return json_encode([
                    'status' => 'true',
                    'data' => ['message' => 'Successful']
                ]);
            }
        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }

        return 'Failed';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Plan::where('id', $id)->get();

        return response()->json(
            $data,
            200,
            ['Content-Type' => 'application/json; charset=UTF-8', 'charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function showForUser($id)
    {
        $user_id = $request->user()->id;
        $data = Plan::find($id)->users()->get();

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

                return json_encode([
                    'status' => 'true',
                    'data' => ['message' => 'Successful']
                ]);
            }

        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }

        return 'Failed';
    }

    public function updateForUser(Request $request)
    {
        $user_id = $request->user()->id;
        try {
            if (Plan::where('user_id', $user_id)->update($request->toArray())) {

                return json_encode([
                    'status' => 'true',
                    'data' => ['message' => 'Successful']
                ]);
            }

        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }

        return 'Failed';
    }

    public function updateForParticipant(Request $request)
    {
        try {
            if (Plan::where('participant_id', $participant_id)->update($request->toArray())) {

                return json_encode([
                    'status' => 'true',
                    'data' => ['message' => 'Successful']
                ]);
            }

        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }

        return 'Failed';
    }

    public function applyForPlan(Request $request, $id)
    {
        try {
            $user_id = $request->user()->id;
            $plan = Plan::find($id);
            if ($plan->users()->attach($user_id)) {

                return json_encode([
                    'status' => 'true',
                    'data' => ['message' => 'Successful']
                ]);
            }

        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }

        return 'Failed';
    }

    public function acceptApplicationForPlan(Request $request, $id)
    {
        try {
            $data = $request->toArray();
            if (Plan::where('id', $id)->update(['participant_id' => $data['participant_id']])) {

                return json_encode([
                    'status' => 'true',
                    'data' => ['message' => 'Successful']
                ]);
            }

        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }

        return 'Failed';
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

                return json_encode([
                    'status' => 'true',
                    'data' => ['message' => 'Successful']
                ]);
            }

        } catch (Exception $e) {
            \Log::info($e->getMessage());
        }

        return 'destroy param id = ' . $id;
    }
}
