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
use Lasallecms\Formhandling\AdminFormhandling\Command;

// Laravel classes
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Container\Container as Container;

// Laravel facades
use Illuminate\Support\Facades\Input;

class CreateCommand extends Command implements SelfHandling
{
    use DispatchesCommands;

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        // Create a new command instance
        $newCreateCommand = new CreateCommand;


        // get the field list array
        $field_list = $this->getFieldList();


        // add the field_list itself to new command instance
        $newCreateCommand->field_list = $field_list;

        // add crud_action to the new command instance
        $newCreateCommand->crud_action = Input::get('crud_action');

        // add fields with their associated data to the new command instance
        foreach( $field_list as $field )
        {
            if ( $field['name'] == "id" ) continue;

            $newCreateCommand->$field['name'] = Input::get($field['name']);
        }

        // Get the namespace and class name of the Form Processor class
        $namespace_formprocessor        = $this->getNamespaceFormprocessor();
        $classname_formprocessor_create = $this->getClassnameFormprocessorCreate();

        // Inject a new instance of the container, in order to inject the relevant model.
        $this->app   = new Container;

        // bind the Form Processor class to the IoC (that is, inject the Form Processor class into the IoC)
        $this->$classname_formprocessor_create = $this->app->make( $namespace_formprocessor."\\".$classname_formprocessor_create );

        // Now onto the Form Processor, aka "The Command Handler"
        return $this->$classname_formprocessor_create->quarterback($newCreateCommand);
    }
}