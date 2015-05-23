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

// Laravel facades
use Illuminate\Support\Facades\Input;

abstract class Command
{
    /*
     * Get the field_list from the request
     *
     * @return array
     */
    public function getFieldList()
    {
        // paramater "true" returns array
        // http://php.net/manual/en/function.json-decode.php
        return json_decode(Input::get('field_list'), true);
    }

    /*
     * Get the namespace_formprocessor from the request
     *
     * @return string
     */
    public function getNamespaceFormprocessor()
    {
        return Input::get('namespace_formprocessor');
    }

    /*
     * Get the classname_formprocessor_create from the request
     *
     * @return string
     */
    public function getClassnameFormprocessorCreate()
    {
        return Input::get('classname_formprocessor_create');
    }

    /*
     * Get the classname_formprocessor_update from the request
     *
     * @return string
     */
    public function getClassnameFormprocessorUpdate()
    {
        return Input::get('classname_formprocessor_update');
    }

    /*
     * Get the classname_formprocessor_delete from the request
     *
     * @return string
     */
    public function getClassnameFormprocessorDelete()
    {
        return Input::get('classname_formprocessor_delete');
    }
}