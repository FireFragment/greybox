<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// TODO: Delete
$router->get('prokop', ['uses' => 'PersonController@prokop']);

$router->group(['prefix' => 'api'], function () use ($router) {
	$router->get('user',  ['uses' => 'UserController@showAll']);
	$router->get('user/{id}', ['uses' => 'UserController@showOne']);
	$router->post('user', ['uses' => 'UserController@create']);
	$router->delete('user/{id}', ['uses' => 'UserController@delete']);
	$router->put('user/{id}', ['uses' => 'UserController@update']);
	$router->put('user/{id}/password', ['uses' => 'UserController@updatePassword']);
	$router->get('user/{userId}/person[/event/{eventId}]', ['uses' => 'UserController@showPeople']);
    $router->get('user/{userId}/team[/event/{eventId}]', ['uses' => 'UserController@showTeams']);
    $router->get('user/{id}/client', ['uses' => 'UserController@showClients']);
    $router->get('user/{id}/debate', ['uses' => 'DebateController@showDebatesForUser']);

	$router->post('login', ['uses' => 'UserController@login']);
	$router->post('logout', ['uses' => 'UserController@logout']);
	$router->post('reset', ['uses' => 'UserController@sendResetPasswordEmail']);
	$router->put('reset', ['uses' => 'UserController@resetPassword']);

	$router->get('registration',  ['uses' => 'RegistrationController@showAll']);
	$router->get('registration/{id}', ['uses' => 'RegistrationController@showOne']);
	$router->post('registration', ['uses' => 'RegistrationController@create']);
	$router->delete('registration/{id}', ['uses' => 'RegistrationController@delete']);
	$router->put('registration/{id}', ['uses' => 'RegistrationController@update']);
	$router->put('registration/{id}/confirm', ['uses' => 'RegistrationController@confirm']);

	$router->get('team',  ['uses' => 'TeamController@showAll']);
	$router->get('team/{id}', ['uses' => 'TeamController@showOne']);
	$router->post('team', ['uses' => 'TeamController@create']);
	$router->delete('team/{id}', ['uses' => 'TeamController@delete']);
	$router->put('team/{id}', ['uses' => 'TeamController@update']);
	$router->post('team/merge', ['uses' => 'TeamController@merge']);

    $router->get('institution',  ['uses' => 'InstitutionController@showAll']);
    $router->get('institution/{id}', ['uses' => 'InstitutionController@showOne']);
    $router->post('institution', ['uses' => 'InstitutionController@create']);
    $router->delete('institution/{id}', ['uses' => 'InstitutionController@delete']);
    $router->put('institution/{id}', ['uses' => 'InstitutionController@update']);

    $router->get('event',  ['uses' => 'EventController@showAll']);
    $router->get('event/{id}', ['uses' => 'EventController@showOne']);
    $router->post('event', ['uses' => 'EventController@create']);
    $router->delete('event/{id}', ['uses' => 'EventController@delete']);
    $router->put('event/{id}', ['uses' => 'EventController@update']);
    $router->get('event/{id}/registration', ['uses' => 'EventController@showRegistrations']);
    $router->get('event/{eventId}/user/{userId}/registration', ['uses' => 'EventController@showUserRegistrations']);
    $router->get('event/{eventId}/draw/{roundNumber}', ['uses => EventController@draw']);

    $router->get('person',  ['uses' => 'PersonController@showAll']);
    $router->get('person/{id}', ['uses' => 'PersonController@showOne']);
    $router->post('person', ['uses' => 'PersonController@create']);
    $router->delete('person/{id}', ['uses' => 'PersonController@delete']);
    $router->put('person/{id}', ['uses' => 'PersonController@update']);

    $router->get('role',  ['uses' => 'RoleController@showAll']);
    $router->get('role/{id}', ['uses' => 'RoleController@showOne']);
    $router->post('role', ['uses' => 'RoleController@create']);
    $router->delete('role/{id}', ['uses' => 'RoleController@delete']);
    $router->put('role/{id}', ['uses' => 'RoleController@update']);

    $router->get('client',  ['uses' => 'ClientController@showAll']);
    $router->get('client/{id}', ['uses' => 'ClientController@showOne']);
    $router->post('client', ['uses' => 'ClientController@create']);
    $router->delete('client/{id}', ['uses' => 'ClientController@delete']);
    $router->put('client/{id}', ['uses' => 'ClientController@update']);
    $router->get('clientfakturoid', ['uses' => 'ClientController@showAllFromFakturoid']);

    $router->get('invoice',  ['uses' => 'InvoiceController@showAll']);
    $router->get('invoice/{id}', ['uses' => 'InvoiceController@showOne']);
    $router->post('invoice', ['uses' => 'InvoiceController@create']);
    $router->delete('invoice/{id}', ['uses' => 'InvoiceController@delete']);
    $router->put('invoice/{id}', ['uses' => 'InvoiceController@update']);
    //$router->get('invoice/{id}/pdf', ['uses' => 'InvoiceController@showOnePdf']);
    //$router->put('invoice/', ['uses' => 'InvoiceController@massUpdate']);

    $router->get('price',  ['uses' => 'PriceController@showAll']);
    $router->get('price/{id}', ['uses' => 'PriceController@showOne']);
    $router->post('price', ['uses' => 'PriceController@create']);
    $router->delete('price/{id}', ['uses' => 'PriceController@delete']);
    $router->put('price/{id}', ['uses' => 'PriceController@update']);

    $router->get('translation',  ['uses' => 'TranslationController@showAll']);
    $router->get('translation/{id}', ['uses' => 'TranslationController@showOne']);
    $router->post('translation', ['uses' => 'TranslationController@create']);
    $router->delete('translation/{id}', ['uses' => 'TranslationController@delete']);
    $router->put('translation/{id}', ['uses' => 'TranslationController@update']);

    $router->post('deletedautofill', ['uses' => 'DeletedAutofillController@create']);

    $router->get('dietaryrequirement',  ['uses' => 'DietaryRequirementController@showAll']);
    $router->get('dietaryrequirement/{id}', ['uses' => 'DietaryRequirementController@showOne']);

    $router->get('country/cs', ['uses' => 'CountryController@showCzech']);
    $router->get('country/en', ['uses' => 'CountryController@showEnglish']);
    $router->get('country', ['uses' => 'CountryController@showBoth']);

    $router->get('motion',  ['uses' => 'MotionController@showAll']);
    $router->get('motion/{id}', ['uses' => 'MotionController@showOne']);
    $router->post('motion', ['uses' => 'MotionController@create']);
    $router->delete('motion/{id}', ['uses' => 'MotionController@delete']);
    $router->put('motion/{id}', ['uses' => 'MotionController@update']);

    $router->get('motioncategory',  ['uses' => 'MotionCategoryController@showAll']);
    $router->get('motioncategory/{id}', ['uses' => 'MotionCategoryController@showOne']);
    $router->post('motioncategory', ['uses' => 'MotionCategoryController@create']);
    $router->delete('motioncategory/{id}', ['uses' => 'MotionCategoryController@delete']);
    $router->put('motioncategory/{id}', ['uses' => 'MotionCategoryController@update']);

    $router->get('debate',  ['uses' => 'DebateController@showAll']);
    $router->get('debate/{id}', ['uses' => 'DebateController@showOne']);
    $router->post('debate', ['uses' => 'DebateController@create']);
    $router->delete('debate/{id}', ['uses' => 'DebateController@delete']);
    $router->put('debate/{id}', ['uses' => 'DebateController@update']);
});