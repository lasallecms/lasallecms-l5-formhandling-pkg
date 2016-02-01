<?php

namespace Lasallecms\Formhandling\AdminFormhandling;

/**
 *
 * Form handling package for the LaSalle Content Management System, based on the Laravel 5 Framework
 * Copyright (C) 2015  The South LaSalle Trading Corporation
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @package    Form Table handling package for the LaSalle Content Management System
 * @link       http://LaSalleCMS.com
 * @copyright  (c) 2015, The South LaSalle Trading Corporation
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 * @author     The South LaSalle Trading Corporation
 * @email      info@southlasalle.com
 *
 */

// LaSalle Software
use Lasallecms\Helpers\Dates\DatesHelper;
use Lasallecms\Helpers\HTML\HTMLHelper;

// Command bus commands
use Lasallecms\Formhandling\AdminFormhandling\CreateCommand;
use Lasallecms\Formhandling\AdminFormhandling\UpdatePostCommand;
use Lasallecms\Formhandling\AdminFormhandling\DeletePostCommand;

// Laravel classes
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;

// Laravel Facades
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

// Third party classes
use Carbon\Carbon;
use Collective\Html\FormFacade as Form;

// FYI: the template is the same name as the one specified in the LaSalleCMS Admin package's config

/**
 * Class AdminFormBaseController
 * @package Lasallecms\Formhandling\AdminFormhandling
 */
abstract class AdminFormBaseController extends BaseController
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * Repository
     *
     * @var Lasallecms\Lasallecmsapi\Repositories\BaseRepository
     */
    protected $repository;

    /**
     * @var  namespace and class of relevant model
     */
    protected $model;


    /**
     * Middleware!
     */
    public function __construct() {
        // User must be logged to access everything in this package
        $this->middleware(\Lasallecms\Helpers\Middleware\MustBeLoggedInCheck::class);

        // Logged in user must pass these checks too
        $this->middleware(\Lasallecms\Helpers\Middleware\CustomAdminAuthChecks::class);
    }


    /**
     * Display a listing
     * GET /{table}/index
     *
     * @return Response
     */
    public function index() {
        // Is this user allowed to do this?
        if (!$this->repository->isUserAllowed('index'))
        {
            Session::flash('status_code', 400 );
            $message = "You are not allowed to view the list of ".$this->table_name;
            Session::flash('message', $message);
            return view('formhandling::warnings/' . config('lasallecmsadmin.admin_template_name') . '/user_not_allowed', [
                'package_title'        => $this->model->package_title,
                'table_type_plural'    => $this->model->table,
                'table_type_singular'  => strtolower($this->model->model_class),
                'resource_route_name'  => $this->resource_route_name,
                'HTMLHelper'           => HTMLHelper::class,
            ]);
        }


        // If this user has locked records for this table, then unlock 'em
        $this->repository->unlockMyRecords($this->model->table);

        return view('formhandling::adminformhandling/' . config('lasallecmsadmin.admin_template_name') . '/index',
        [
            'display_the_view_button'      => $this->model->display_the_view_button,
            'records'                      => $this->repository->getAll(),
            'repository'                   => $this->repository,
            'package_title'                => $this->model->package_title,
            'table_name'                   => $this->model->table,
            'model_class'                  => $this->model->model_class,
            'resource_route_name'          => $this->model->resource_route_name,
            'field_list'                   => $this->getFieldList(),
            'suppress_delete_button_when_one_record' => $this->model->suppress_delete_button_when_one_record,
            'DatesHelper'                  => DatesHelper::class,
            'HTMLHelper'                   => HTMLHelper::class,
            'carbon'                       => Carbon::class,
            'Config'                       => Config::class,
            'Form'                         => Form::class,
        ]);
    }


    /**
     * CREATE form
     * GET /{table}/create
     *
     * @return Response
     */
    public function create() {
        // Is this user allowed to do this?
        if (!$this->repository->isUserAllowed('create'))
        {
            Session::flash('status_code', 400 );
            $message = "You are not allowed to create ".$this->model->table;
            Session::flash('message', $message);
            return view('formhandling::warnings/' . config('lasallecmsadmin.admin_template_name') . '/user_not_allowed', [
                'package_title'        => $this->model->package_title,
                'table_type_plural'    => $this->model->table,
                'table_type_singular'  => strtolower($this->model->model_class),
                'resource_route_name'  => $this->model->resource_route_name,
                'HTMLHelper'           => HTMLHelper::class,
            ]);
        }

        // Specifically for Post Updates
        if (empty(Input::get('post_id'))) {
            $post_id = 0;
            //$related_table

        } else {

            $post_id = Input::get('post_id');

            if ( (int) $post_id < 1 )
            {
                // flash message with redirect
                Session::flash('status_code', 400 );
                $message = 'Please initiate the creation of a new update for your post by clicking the icon ';
                $message .= 'in the row of the post you want to update.';
                Session::flash('message', $message);
                return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
            }
        }

        // Are there mandatory related tables that have no records? If so, can't create/edit
        $isNotNullableRelatedTablesWithNoRecords = $this->repository->isNotNullableRelatedTablesWithNoRecords($this->getFieldList());

        if ($isNotNullableRelatedTablesWithNoRecords['mandatory_no_records'])
        {
            Session::flash('status_code', 400 );
            $message  = "You are not allowed to create ".HTMLHelper::pluralToSingular($this->model->table)." because ";
            $message .= "there are no enabled records in the ";

            if ( !empty($isNotNullableRelatedTablesWithNoRecords['field']['alternate_form_name']) )
            {
                $message .= $isNotNullableRelatedTablesWithNoRecords['field']['alternate_form_name'];
            } else {
                $message .= $isNotNullableRelatedTablesWithNoRecords['field']['name'];
            }

            $message .= ' table. You must add enabled records to the "';
            $message .= $isNotNullableRelatedTablesWithNoRecords['field']['related_table_name'];

            $message .= '" table first!';
            Session::flash('message', $message);
            return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
        }

        return view('formhandling::adminformhandling/' . config('lasallecmsadmin.admin_template_name') . '/create',
        [
            'post_id'                      => $post_id,
            'repository'                   => $this->repository,
            'package_title'                => $this->model->package_title,
            'table_name'                   => $this->model->table,
            'model_class'                  => $this->model->model_class,
            'resource_route_name'          => $this->model->resource_route_name,
            'field_list'                   => $this->getFieldList(),
            'namespace_formprocessor'      => $this->model->namespace_formprocessor,
            'classname_formprocessor_create' => $this->model->classname_formprocessor_create,
            'DatesHelper'                  => DatesHelper::class,
            'HTMLHelper'                   => HTMLHelper::class,
            'carbon'                       => Carbon::class,
            'Config'                       => Config::class,
            'Form'                         => Form::class,
            'admin_size_input_text_box'    => Config::get('lasallecmsadmin.admin_size_input_text_box'),
        ]);
    }


    /**
     * Store a newly created resource in storage
     * POST admin/{table}/create
     *
     * @param  Request   $request
     * @return Response
     */
    public function store(Request $request) {
	// Grab this input to determine if we need to return to the edit form after updating
        $return_to_edit = $request->input('return_to_edit');


        $response = $this->dispatchFrom(CreateCommand::class, $request);

        Session::flash('status_code', $response['status_code'] );

        if ($response['status_text'] == "validation_failed")
        {
            if (is_object($response['errorMessages'])) {
                Session::flash('message', $response['errorMessages']->first());
            } else {
                Session::flash('message', $response['errorMessages']);
            }

            // Return to the form with error messages
            return Redirect::back()
                ->withInput($response['data'])
                ->withErrors($response['errorMessages']);
        }


        if ($response['status_text'] == "persist_failed")
        {
            $message = "Persist failed. It does not happen often, but Laravel's save failed. The database operation is called at Lasallecms\Lasallecmsapi\\".$this->model->table."\\".$this->model->classname_formprocessor_delete.". MySQL probably hiccupped, so probably just try again.";
            Session::flash('message', $message);

            // Return to the form with error messages
            return Redirect::back()
                ->withInput($response['data']);
        }

        if ( empty($response['data']['title']) )
        {
            $message =  "You successfully created your new ".strtolower($this->model->model_class)."!";
        } else {
            $message =  "You successfully created the ".strtolower($this->model->model_class)." ".strtoupper($response['data']['title'])."!";
        }
        Session::flash('message', $message);


        if (!isset($response['data']['title'])) {
            return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
        }

        // Redirect to the edit form?
        $record_id = DB::table(strtolower($this->model->table))->where('title', '=', $response['data']['title'])->value('id');
        if ( 
            ($return_to_edit == "Save & Edit") 
            && (!empty($this->model->table)) 
            && (!empty($response['data']['title'])) 
            && ($record_id > 0) 
        ) {
            return Redirect::route('admin.'.$this->model->resource_route_name.'.edit', $record_id);
        }


        return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
    }


    /**
     * Display the specified resource
     * GET /{table}/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        if ($this->model->display_the_view_button)
        {
            return view('formhandling::adminformhandling/' . config('lasallecmsadmin.admin_template_name') . '/show',
                [
                    'display_the_view_button'      => $this->model->display_the_view_button,
                    'repository'                   => $this->repository,
                    'record'                       => $this->repository->getFind($id),
                    'package_title'                => $this->model->package_title,
                    'table_name'                   => $this->model->table,
                    'model_class'                  => $this->model->model_class,
                    'resource_route_name'          => $this->model->resource_route_name,
                    'field_list'                   => $this->getFieldList(),
                    'namespace_formprocessor'      => $this->model->namespace_formprocessor,
                    'classname_formprocessor_update' => $this->model->classname_formprocessor_update,
                    'DatesHelper'                  => DatesHelper::class,
                    'HTMLHelper'                   => HTMLHelper::class,
                    'carbon'                       => Carbon::class,
                    'Config'                       => Config::class,
                    'Form'                         => Form::class,
                ]);

        } else {
            return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
        }
    }


    /**
     * EDIT form
     * GET /{table}/{id}/edit
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        // Is this user allowed to do this?
        if (!$this->repository->isUserAllowed('edit'))
        {
            Session::flash('status_code', 400 );
            $message = "You are not allowed to edit ".$this->model->table;
            Session::flash('message', $message);
            return view('formhandling::warnings/' . config('lasallecmsadmin.admin_template_name') . '/user_not_allowed', [
                'package_title'        => $this->model->package_title,
                'table_type_plural'    => $this->model->table,
                'table_type_singular'  => strtolower($this->model->model_class),
                'resource_route_name'  => $this->model->resource_route_name,
                'HTMLHelper'           => HTMLHelper::class,
            ]);
        }


        // Is this record locked?
        if ($this->repository->isLocked($id))
        {
            $modelClass = HTMLHelper::properPlural($this->model->model_class);

            $message = "This ".$modelClass." is not available for editing, as someone else is currently editing this ".$modelClass.".";
            Session::flash('message', $message);
            Session::flash('status_code', 400 );
            return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
        }

        // Lock the record
        $this->repository->populateLockFields($id);

        // Are there mandatory related tables that have no records? If so, can't create/edit
        $isNotNullableRelatedTablesWithNoRecords = $this->repository->isNotNullableRelatedTablesWithNoRecords($this->getFieldList());

        if ($isNotNullableRelatedTablesWithNoRecords['mandatory_no_records'])
        {
            Session::flash('status_code', 400 );
            $message  = "You are not allowed to update ".HTMLHelper::pluralToSingular($this->model->table)." because ";
            $message .= "there are no enabled records in the ";

            if ( !empty($isNotNullableRelatedTablesWithNoRecords['field']['alternate_form_name']) )
            {
                $message .= $isNotNullableRelatedTablesWithNoRecords['field']['alternate_form_name'];
            } else {
                $message .= $isNotNullableRelatedTablesWithNoRecords['field']['name'];
            }

            $message .= ' table. You must add enabled records to the "';
            $message .= $isNotNullableRelatedTablesWithNoRecords['field']['related_table_name'];

            $message .= '" table first!';
            Session::flash('message', $message);
            return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
        }


        return view('formhandling::adminformhandling/' . config('lasallecmsadmin.admin_template_name') . '/edit',
        [
            'repository'                   => $this->repository,
            'record'                       => $this->repository->getFind($id),
            'package_title'                => $this->model->package_title,
            'table_name'                   => $this->model->table,
            'model_class'                  => $this->model->model_class,
            'resource_route_name'          => $this->model->resource_route_name,
            'field_list'                   => $this->getFieldList(),
            'namespace_formprocessor'      => $this->model->namespace_formprocessor,
            'classname_formprocessor_update' => $this->model->classname_formprocessor_update,
            'DatesHelper'                  => DatesHelper::class,
            'HTMLHelper'                   => HTMLHelper::class,
            'carbon'                       => Carbon::class,
            'Config'                       => Config::class,
            'Form'                         => Form::class,
            'admin_size_input_text_box'    => Config::get('lasallecmsadmin.admin_size_input_text_box'),
        ]);
    }


    /**
     * Update the specific post in the db
     * PUT /{table}/{id}
     *
     * @param  Request   $request
     * @return Response
     */
    public function update(Request $request) {
	// Grab these inputs to determine if we need to return to the edit form after updating
        $return_to_edit = $request->input('return_to_edit');
        $record_id      = $request->input('id');

        $response = $this->dispatchFrom(UpdateCommand::class, $request);


        Session::flash('status_code', $response['status_code'] );

        if ($response['status_text'] == "validation_failed")
        {
            // flash message
            if (is_object($response['errorMessages'])) {
                Session::flash('message', $response['errorMessages']->first());
            } else {
                Session::flash('message', $response['errorMessages']);
            }

            // Return to the edit form with error messages
            return Redirect::back()
                ->withInput($response['data'])
                ->withErrors($response['errorMessages']);
        }


        if ($response['status_text'] == "persist_failed")
        {
            $message = "Persist failed. It does not happen often, but Laravel's save failed. The database operation is called at Lasallecms\Lasallecmsapi\\".$this->model->table."\\".$this->model->classname_formprocessor_delete.". MySQL probably hiccupped, so probably just try again.";
            Session::flash('message', $message);

            // Return to the edit form with error messages
            return Redirect::back()
                ->withInput($response['data']);
        }


        if ( empty($response['data']['title']) )
        {

            if (!empty($response['data']['composite_title']))
            {
                $message = "You successfully updated the ".strtolower($this->model->model_class)." ".strtoupper($response['data']['composite_title'])."!";

            } else {
                $message =  "You successfully updated your new ".strtolower($this->model->model_class)."!";
            }

        } else {
            $message =  "You successfully updated the ".strtolower($this->model->model_class)." ".strtoupper($response['data']['title'])."!";
        }
        Session::flash('message', $message);


        // Redirect to the edit form?
        if ($return_to_edit == "Save & Edit") {
            return Redirect::route('admin.'.$this->model->resource_route_name.'.edit', $record_id);
        }


        return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
    }





    /**
     * Confirm the deletion
     *
     * The javascript that I use works everywhere except for posts. I thought, well,
     * I tend to be distracted when I do deletions; plus, I have a funny type of
     * muscle memory when it comes to gray system-ish model pop-up message boxes.
     * When a system confirm pops-up, 99% I click "ok". I should have a confirmation
     * that actually gets my attention -- and to use the trick where "cancel" on the right
     * due to "ok" muscle memory tendency.
     *
     * @param  int      $id     NOTE: *NOT* passing the REQUEST object
     * @return Response
     */
    public function confirmDeletion($id) {
        return view('formhandling::adminformhandling/' . config('lasallecmsadmin.admin_template_name') . '/delete_confirm',
            [
                'user'                         => Auth::user(),
                'repository'                   => $this->repository,
                'record'                       => $this->repository->getFind($id),
                'package_title'                => $this->model->package_title,
                'table_name'                   => $this->model->table,
                'model_class'                  => $this->model->model_class,
                'resource_route_name'  => $this->model->resource_route_name,
                'HTMLHelper'                   => HTMLHelper::class,
                'Config'                       => Config::class,
                'Form'                         => Form::class,
            ]);
    }


    /**
     * Confirm multiple row deletions
     *
     * @return Response
     */
    public function confirmDeletionMultipleRows(Request $request) {

        $checkboxes = $request->input('checkbox');

        if (!$checkboxes) {
            // flash message with redirect
            Session::flash('status_code', 400 );
            $message = 'You have not selected any rows for deletion.';
            Session::flash('message', $message);
            return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
        }


        // Prepare array of records

        // i) flatten the array (https://laravel.com/docs/5.1/helpers#method-array-flatten)
        $flattenedChecklistArray = array_flatten($checkboxes);

        // ii) find multiple records
        $records = $this->model->findMany($flattenedChecklistArray);

        return view('formhandling::adminformhandling/' . config('lasallecmsadmin.admin_template_name') . '/delete_confirmMultipleRows',
            [
                'user'                         => Auth::user(),
                'repository'                   => $this->repository,
                'records'                      => $records,
                'package_title'                => $this->model->package_title,
                'table_name'                   => $this->model->table,
                'model_class'                  => $this->model->model_class,
                'checkboxes'                   => $checkboxes,
                'resource_route_name'          => $this->model->resource_route_name,
                'HTMLHelper'                   => HTMLHelper::class,
                'Config'                       => Config::class,
                'Form'                         => Form::class,
            ]);
    }





    /**
     * Remove the specific post from the db
     * DELETE /{table}/{id}
     *
     * This method is not routed through a REQUEST, unfortunately. So,
     * using a post collection as the array access-ible object. Remember,
     * Laravel's command bus needs an array access-ible object!
     * Also, note using $this->dispatch(), not $this->dispatchFrom().
     *
     * @param  int      $id     NOTE: *NOT* passing the REQUEST object
     * @return Response
     */
    public function destroy($id)
    {
        // Create the data array
        $data = [
            'id'                             => $id,
            'classname_formprocessor_delete' => $this->model->classname_formprocessor_delete,
            'namespace_formprocessor'        => $this->model->namespace_formprocessor,
        ];


        // Is this user allowed to do this?
        if (!$this->repository->isUserAllowed('destroy'))
        {
            Session::flash('status_code', 400 );
            $message = "You are not allowed to delete this".strtolower($this->model->model_class);
            Session::flash('message', $message);
            return view('formhandling::warnings/' . config('lasallecmsadmin.admin_template_name') . '/user_not_allowed', [
                'package_title'        => $this->model->package_title,
                'table_type_plural'    => $this->model->table,
                'table_type_singular'  => strtolower($this->model->model_class),
                'resource_route_name'  => $this->model->resource_route_name,
                'HTMLHelper'           => HTMLHelper::class,
            ]);
        }

        // Is this record not supposed to be deleted?
        if ($this->repository->doNotDelete($id))
        {
            Session::flash('status_code', 400 );
            $message = 'This '.strtolower($this->model->model_class).' is a core lookup record, so you cannot delete it';
            Session::flash('message', $message);
            return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
        }


        // Is this record locked?
        if ($this->repository->isLocked($id))
        {
            $response = 'This ".strtolower($this->model->model_class)." is not available for deletion, as someone else is currently editing this post';
            Session::flash('message', $response);
            Session::flash('status_code', 400 );
            return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
        }


        $recordToBeDeleted = $this->model->findOrFail($id);
        $response = $this->dispatch(new DeleteCommand($data));


        Session::flash('status_code', $response['status_code'] );


        if ($response['status_text'] == "foreign_key_check_failed")
        {
            $message = "Cannot delete this ".strtolower($this->model->model_class)." because it is in use.";
            Session::flash('message', $message);

            // Return to the index listing with error messages
            return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
        }


        if ($response['status_text'] == "persist_failed")
        {
            $message = "Persist failed. It does not happen often, but Laravel's deletion failed. The database operation is called at Lasallecms\Lasallecmsapi\\".$this->model->table."\\".$this->model->classname_formprocessor_delete.". MySQL probably hiccupped, so probably just try again.";
            Session::flash('message', $message);

            // Return to the index listing with error messages
            return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
        }


        if ( empty($recordToBeDeleted->title) )
        {
            $message =  "You successfully deleted the record!";
        } else {
            $message =  "You successfully deleted the ".strtolower($this->model->model_class)." ".strtoupper($recordToBeDeleted->title)."!";
        }
        Session::flash('message', $message);
        return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
    }



    public function destroyMultipleRecords(Request $request) {

        // https://laravel.com/docs/5.1/eloquent#deleting-models

        $checkboxes = $request->input('checkbox');

        if (!$checkboxes) {
            // flash message with redirect
            Session::flash('status_code', 400 );
            $message = 'You have not selected any rows for deletion.';
            Session::flash('message', $message);
            return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
        }



        // Prepare array of records

        // i) flatten the array (https://laravel.com/docs/5.1/helpers#method-array-flatten)
        $flattenedChecklistArray = array_flatten($checkboxes);

        // ii) find multiple records
        $records = $this->model->findMany($flattenedChecklistArray);

        foreach ($records as $record) {

            // Is this user allowed to do this?
            if (!$this->repository->isUserAllowed('destroy'))
            {
                Session::flash('status_code', 400 );
                $message = "You are not allowed to delete this".strtolower($this->model->model_class);
                Session::flash('message', $message);
                return view('formhandling::warnings/' . config('lasallecmsadmin.admin_template_name') . '/user_not_allowed', [
                    'package_title'        => $this->model->package_title,
                    'table_type_plural'    => $this->model->table,
                    'table_type_singular'  => strtolower($this->model->model_class),
                    'resource_route_name'  => $this->model->resource_route_name,
                    'HTMLHelper'           => HTMLHelper::class,
                ]);
            }

            // Is this record not supposed to be deleted?
            if ($this->repository->doNotDelete($record->id))
            {
                Session::flash('status_code', 400 );
                $message = 'This '.strtolower($this->model->model_class).' is a core lookup record, so you cannot delete it';
                Session::flash('message', $message);
                return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
            }


            // Is this record locked?
            if ($this->repository->isLocked($record->id))
            {
                $response = 'This ".strtolower($this->model->model_class)." is not available for deletion, as someone else is currently editing this post';
                Session::flash('message', $response);
                Session::flash('status_code', 400 );
                return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
            }


            // Create the data array
            $data = [
                'id'                             => $record->id,
                'classname_formprocessor_delete' => $this->model->classname_formprocessor_delete,
                'namespace_formprocessor'        => $this->model->namespace_formprocessor,
            ];

            $response = $this->dispatch(new DeleteCommand($data));


            Session::flash('status_code', $response['status_code'] );


            if ($response['status_text'] == "foreign_key_check_failed")
            {
                $message = "Cannot delete this ".strtolower($this->model->model_class)." because it is in use.";
                Session::flash('message', $message);

                // Return to the index listing with error messages
                return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
            }


            if ($response['status_text'] == "persist_failed")
            {
                $message = "Persist failed. It does not happen often, but Laravel's deletion failed. The database operation is called at Lasallecms\Lasallecmsapi\\".$this->model->table."\\".$this->model->classname_formprocessor_delete.". MySQL probably hiccupped, so probably just try again.";
                Session::flash('message', $message);

                // Return to the index listing with error messages
                return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
            }
        }

        // Still here? Then all the records were deleted successfully.
        $message =  "You successfully deleted all the selected".strtolower(HTMLHelper::properPlural($this->model->model_class))." records!";

        Session::flash('message', $message);
        return Redirect::route('admin.'.$this->model->resource_route_name.'.index');
    }



    //////////////////////////////////////////////////////////////
    ////                Utility type methods                  ////
    //////////////////////////////////////////////////////////////

    /**
     * get the Model's Field List
     *
     * @return array
     */
    public function getFieldLIst()
    {
        if (!isset($this->model->field_list)) {

            return $this->model->getFieldList();
        }

        return $this->model->field_list;
    }
}