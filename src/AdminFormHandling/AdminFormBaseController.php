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

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Foundation\Validation\ValidatesRequests;

# LaSalle Helpers
use Lasallecms\Helpers\HTML\HTMLHelper;
use Lasallecms\Helpers\Dates\DatesHelper;

// Laravel Facades
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

// Note that the template is the same name as the one specified in the LaSalleCMS Admin package's config

abstract class AdminFormBaseController extends BaseController
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
}