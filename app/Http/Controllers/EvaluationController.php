<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\User;

class EvaluationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function post(Request $request){
        $data = $request->toArray();
        $data['evaluated_by'] = $request->user()->id;

        try {
            if (Evaluation::create($data)) {

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

    public function indexOnUser($userId){
        $evaluation = Evaluation::where('user_id', $userId)->latest('created_at')->get();
        $evaluatorImage = User::find($evaluation['evaluated_by'])->image_data;
        $data = [
            'evaluation' => $evaluation,
            'evaluator_image' => $evaluatorImage
        ];

        return response()->json(
            $data,
            200,
            ['Content-Type' => 'application/json; charset=UTF-8', 'charset' => 'utf-8'],
            JSON_UNESCAPED_UNICODE
        );
    }
}
