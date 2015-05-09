<?php
namespace Lasallecms\Formhandling\Lookuptables;

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

/*
 * Yes, I am stuffing a bit too much into my methods. My reasoning:
 *  * lookup table handling is fairly straight forward;
 *  * no command bus, no events;
 *  * step-by-step controller = easy to follow what I am doing
 */

# LaSalle Helpers
use Lasallecms\Helpers\HTML\HTMLHelper;
use Lasallecms\Helpers\Dates\DatesHelper;

// Laravel Facades
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

// Laravel classes
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;

// Note that the template is the same name as the one specified in the LaSalleCMS Admin package's config

abstract class AdminLookupTableBaseController extends BaseController
{

    use DispatchesCommands, ValidatesRequests;

    /*
     * @var namespace/class of the relevant model
     */
    protected $modelNamespaceClass;

    /*
     * Repository
     *
     * @var Lasallecms\Helpers\Repositories\LookupRespository
     */
    protected $repository;


    public function __construct()
    {
        // User must be logged to access everything in this package
        $this->middleware(\Lasallecms\Helpers\Middleware\MustBeLoggedInCheck::class);

        // Logged in user must pass these checks too
        $this->middleware(\Lasallecms\Helpers\Middleware\CustomAdminAuthChecks::class);
    }


    /**
     * Display a listing of the lookup table's records
     * GET /{lookup table}/index
     *
     * @return Response
     */
    public function index()
    {
        // Is this user allowed to do this?
        if (!$this->repository->isUserAllowed('index'))
        {
            Session::flash('status_code', 400 );
            $message = "You are not allowed to view the list of ".$this->table_type_plural;
            Session::flash('message', $message);
            return view('formhandling::warnings/' . config('lasallecmsadmin.admin_template_name') . '/user_not_allowed', [
                'package_title'        => $this->package_title,
                'table_type_plural'    => $this->table_type_plural,
                'table_type_singular'  => $this->table_type_singular,
                'resource_route_name'  => $this->resource_route_name,
                'HTMLHelper'           => HTMLHelper::class,
            ]);
        }

        // If this user has locked records for this table, then unlock 'em
        $this->repository->unlockMyRecords($this->table_name);

        $records = $this->repository->getAll();

        return view('formhandling::lookuptables/' . config('lasallecmsadmin.admin_template_name') . '/index', [
            'package_title'        => $this->package_title,
            'table_type_plural'    => $this->table_type_plural,
            'table_type_singular'  => $this->table_type_singular,
            'resource_route_name'  => $this->resource_route_name,
            'DatesHelper'          => DatesHelper::class,
            'HTMLHelper'           => HTMLHelper::class,
            'records'              => $records,
        ]);
    }


    /**
     * Form to create a new lookup table record
     * GET /{lookup table}/create
     *
     * @return Response
     */
    public function create()
    {
        // Is this user allowed to do this?
        if (!$this->repository->isUserAllowed('create'))
        {
            Session::flash('status_code', 400 );
            $message = "You are not allowed to create ".$this->table_type_plural;
            Session::flash('message', $message);
            return view('formhandling::warnings/' . config('lasallecmsadmin.admin_template_name') . '/user_not_allowed', [
                'package_title'        => $this->package_title,
                'table_type_plural'    => $this->table_type_plural,
                'table_type_singular'  => $this->table_type_singular,
                'resource_route_name'  => $this->resource_route_name,
                'HTMLHelper'           => HTMLHelper::class,
            ]);
        }

        return view('formhandling::lookuptables/' . config('lasallecmsadmin.admin_template_name') . '/create', [
            'package_title'        => $this->package_title,
            'table_type_plural'    => $this->table_type_plural,
            'table_type_singular'  => $this->table_type_singular,
            'resource_route_name'  => $this->resource_route_name,
            'DatesHelper'          => DatesHelper::class,
            'HTMLHelper'           => HTMLHelper::class,
        ]);
    }


    /**
     * Store a newly created resource in storage
     * POST admin/{lookup table}/create
     *
     * @param  Request   $request
     * @return Response
     */
    public function store()
    {
        // Is this user allowed to do this?
        if (!$this->repository->isUserAllowed('store'))
        {
            Session::flash('status_code', 400 );
            $message = "You are not allowed to store ".$this->table_type_plural;
            Session::flash('message', $message);
            return view('formhandling::warnings/' . config('lasallecmsadmin.admin_template_name') . '/user_not_allowed', [
                'package_title'        => $this->package_title,
                'table_type_plural'    => $this->table_type_plural,
                'table_type_singular'  => $this->table_type_singular,
                'resource_route_name'  => $this->resource_route_name,
                'HTMLHelper'           => HTMLHelper::class,
            ]);
        }

        // sanitize
        $rules     = $this->repository->getLookupTablesSanitationRulesForCreate();
        $data      = $this->repository-> getSanitize(Input::all(), $rules);

        // Validate
        $rules     = $this->repository->getLookupTablesValidationRulesForCreate();
        $validator = Validator::make($data,$rules);
        if ($validator->fails())
        {
            Session::flash('status_code', 500 );
            Session::flash('message', $validator->messages()->first());
            return Redirect::back()->withInput($data);
        }

        // Prep the data
        $data['title']       = $this->repository->prepareTitleForPersist($data['title']);
        $data['description'] = $this->repository->prepareDescriptionForPersist($data['description']);
        if (!array_key_exists('enabled', $data)) $data['enabled'] = 0;

        // INSERT
        $lookup = $this->repository->newModelInstance();
        $lookup->title       = $data['title'];
        $lookup->description = $data['description'];
        $lookup->enabled     = $data['enabled'];
        $lookup->created_by  = Auth::user()->id;
        $lookup->updated_by  = Auth::user()->id;

        if (!$lookup->save())
        {
            // Prepare the response array, and then return to the edit form with error messages
            // Laravel's https://github.com/laravel/framework/blob/5.0/src/Illuminate/Database/Eloquent/Model.php
            //  does not prepare a MessageBag object, so we'll whip up an error message in the
            //  originating controller
            Session::flash('status_code', 500 );

            $message = "Save failed. Probably due to a system blip. Please try again.";
            Session::flash('message', $message);
            return Redirect::back()->withInput($data);
        }

        Session::flash('status_code', 200 );

        $title = strtoupper($data['title']);
        $message = 'You successfully created the '.$this->table_type_singular.' "'.$title.'"!';
        Session::flash('message', $message);
        return Redirect::route('admin.'.$this->resource_route_name.'.index');
    }


    /**
     * Display the specified tag
     * GET /{lookup table}/{id}
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        // Do not use show(). Redir to index just in case
        return Redirect::route('admin.'.$this->resource_route_name.'.index');
    }




    /**
     * Show the form for editing a specific tag
     * GET /{lookup table}/{id}/edit
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        // Is this user allowed to do this?
        if (!$this->repository->isUserAllowed('edit'))
        {
            Session::flash('status_code', 400 );
            $message = "You are not allowed to edit ".$this->table_type_plural;
            Session::flash('message', $message);
            return view('formhandling::warnings/' . config('lasallecmsadmin.admin_template_name') . '/user_not_allowed', [
                'package_title'        => $this->package_title,
                'table_type_plural'    => $this->table_type_plural,
                'table_type_singular'  => $this->table_type_singular,
                'resource_route_name'  => $this->resource_route_name,
                'HTMLHelper'           => HTMLHelper::class,
            ]);
        }

        // Is this record locked?
        if ($this->repository->isLocked($id))
        {
            $message = 'This tag is not available for editing, as someone else is currently editing this tag';
            Session::flash('message', $message);
            Session::flash('status_code', 400 );
            return Redirect::route('admin.'.$this->resource_route_name.'.index');
        }

        // Lock the record
        $this->repository->populateLockFields($id);

        return view('formhandling::lookuptables/' . config('lasallecmsadmin.admin_template_name') . '/create', [
            'package_title'       => $this->package_title,
            'table_type_plural'   => $this->table_type_plural,
            'table_type_singular' => $this->table_type_singular,
            'resource_route_name' => $this->resource_route_name,
            'DatesHelper'         => DatesHelper::class,
            'HTMLHelper'          => HTMLHelper::class,
            'record'              => $this->repository->getFind($id),
        ]);
    }


    /**
     * Update the specific tag in the db
     * PUT /{lookup table}/{id}
     *
     * @return Response
     */
    public function update()
    {
        // Sanitize
        $rules     = $this->repository->getLookupTablesSanitationRulesForUpdate();
        $data      = $this->repository->getSanitize(Input::all(), $rules);

        // Unlook the record
        $this->repository->unpopulateLockFields($data['id']);

        // Is this user allowed to do this?
        if (!$this->repository->isUserAllowed('update'))
        {
            Session::flash('status_code', 400 );
            $message = "You are not allowed to update ".$this->table_type_plural;
            Session::flash('message', $message);
            return view('formhandling::warnings/' . config('lasallecmsadmin.admin_template_name') . '/user_not_allowed', [
                'package_title'        => $this->package_title,
                'table_type_plural'    => $this->table_type_plural,
                'table_type_singular'  => $this->table_type_singular,
                'resource_route_name'  => $this->resource_route_name,
                'HTMLHelper'           => HTMLHelper::class,
            ]);
        }

        // Validate
        $rules     = $this->repository->getLookupTablesValidationRulesForUpdate();
        $validator = Validator::make($data,$rules);
        if ($validator->fails())
        {
            Session::flash('status_code', 500 );
            Session::flash('message', $validator->messages()->first());
            return Redirect::back()->withInput($data);
        }

        // Prep data
        $data['title']       = $this->repository->prepareTitleForPersist($data['title']);
        $data['description'] = $this->repository->prepareDescriptionForPersist($data['description']);
        if (!array_key_exists('enabled', $data)) $data['enabled'] = 0;

        // UPDATE
        $lookup = $this->repository->getFind($data['id']);
        $lookup->id          = $data['id'];
        $lookup->title       = $data['title'];
        $lookup->description = $data['description'];
        $lookup->enabled     = $data['enabled'];
        $lookup->created_by  = Auth::user()->id;
        $lookup->updated_by  = Auth::user()->id;

        if (!$lookup->save())
        {
            // Prepare the response array, and then return to the edit form with error messages
            // Laravel's https://github.com/laravel/framework/blob/5.0/src/Illuminate/Database/Eloquent/Model.php
            //  does not prepare a MessageBag object, so we'll whip up an error message in the
            //  originating controller
            Session::flash('status_code', 500 );

            $message = "Update failed. Probably due to a system blip. Please try again.";
            Session::flash('message', $message);
            return Redirect::back()->withInput($data);
        }

        Session::flash('status_code', 200 );

        $title = strtoupper($data['title']);
        $message = 'You successfully updated the '.$this->table_type_singular.' "'.$title.'"!';
        Session::flash('message', $message);
        return Redirect::route('admin.'.$this->resource_route_name.'.index');
    }


    /**
     * Remove the specific record from the db
     * DELETE /{lookup table}/{id}
     *
     * @param  int      $id
     * @return Response
     */
    public function destroy($id)
    {
        // Is this user allowed to do this?
        if (!$this->repository->isUserAllowed('destroy'))
        {
            Session::flash('status_code', 400 );
            $message = "You are not allowed to delete ".$this->table_type_plural;
            Session::flash('message', $message);
            return view('formhandling::warnings/' . config('lasallecmsadmin.admin_template_name') . '/user_not_allowed', [
                'package_title'        => $this->package_title,
                'table_type_plural'    => $this->table_type_plural,
                'table_type_singular'  => $this->table_type_singular,
                'resource_route_name'  => $this->resource_route_name,
                'HTMLHelper'           => HTMLHelper::class,
            ]);
        }

        // Is this record locked?
        if ($this->repository->isLocked($id))
        {
            $message = 'This tag is not available for deletion, as someone else is currently editing this '.$this->table_type_singular;
            Session::flash('message', $message);
            Session::flash('status_code', 400 );
            return Redirect::route('admin.'.$this->resource_route_name.'.index');
        }


        // Do other tables use this lookup table ID?
        // are there records that use this lookup table record?
        $foreignKeyChecks = $this->repository->foreignKeyChecks($id);
        foreach ($foreignKeyChecks as $foreignKeyCheck)
        {
            if ($foreignKeyCheck['count'] > 0)
            {
                $message = "Foreign key constraint! The table ".strtoupper($foreignKeyCheck['related_table'])." is using this record ".$foreignKeyCheck['count']." times. Can only delete this record when no other tables are using it.";
                Session::flash('message', $message);
                Session::flash('status_code', 400 );
                return Redirect::route('admin.'.$this->resource_route_name.'.index');
            }
        }

        $title = $this->repository->getFind($id)->title;;

        if ( !$this->repository->getDestroy($id) )
        {
            $message = "Deletion failed. Probably due to a system blip. Please try again.";
            Session::flash('status_code', 500 );
            Session::flash('message', $message);
            return Redirect::route('admin.'.$this->resource_route_name.'.index');
        }

        Session::flash('status_code', 200 );

        $title = strtoupper($title);
        $message = 'You successfully deleted the '.$this->table_type_singular.' "'.$title.'"!';
        Session::flash('message', $message);
        return Redirect::route('admin.'.$this->resource_route_name.'.index');

    }



}
