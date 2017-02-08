<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/auth/register','Auth\RegisterController@createForApi');

Route::post('/auth/isRegistered','Auth\RegisterController@isRegistered');

Route::get('/auth/user/','Auth\ProfileController@show');

Route::put('/auth/user/update', 'Auth\ProfileController@update');

Route::put('/auth/user/update_fcm_token', 'Auth\ProfileController@updateFcmToken');

Route::get('/user/{id}','UserController@show');

Route::delete('/user/{id}','UserController@destroy');

Route::resource('/plans', 'PlanController');

Route::get('/plans/first_index/number/{num}','PlanController@firstIndex');

Route::get('/plans/creator_id/{creator_id}','PlanController@showForCreator');

Route::get('/plans/participant_id/{participant_id}','PlanController@showForParticipant');

Route::get('/plans/last_plan_id/{id}/number/{num}','PlanController@showPlansUnderParam');

Route::put('/plans/{id}/apply','PlanController@applyForPlan');

Route::get('/plans/{id}/applicants','PlanController@showApplicantsForPlan');

Route::put('/plans/{id}/accept','PlanController@acceptApplicationForPlan');
