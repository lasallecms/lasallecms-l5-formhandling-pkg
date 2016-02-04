<?php
namespace Lasallecms\Formhandling\AdminFormhandling;

/**
 *
 * Form handling package for the LaSalle Content Management System, based on the Laravel 5 Framework
 * Copyright (C) 2015 - 2016  The South LaSalle Trading Corporation
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
 * @copyright  (c) 2015 - 2016, The South LaSalle Trading Corporation
 * @license    http://www.gnu.org/licenses/gpl-3.0.html
 * @author     The South LaSalle Trading Corporation
 * @email      info@southlasalle.com
 *
 */

// LaSalle Software
use Lasallecms\Formhandling\AdminFormhandling\Command;

// Laravel classes
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Container\Container as Container;


class DeleteCommand extends Command implements SelfHandling
{
    use DispatchesJobs;

    /*
     * Grab the ID
     *
     * @param  array
     */
    public function __construct($data)
    {
        $this->id                             = $data['id'];
        $this->namespace_formprocessor        = $data['namespace_formprocessor'];
        $this->classname_formprocessor_delete = $data['classname_formprocessor_delete'];
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        // Get the namespace and class name of the Form Processor class
        $namespace_formprocessor        = $this->namespace_formprocessor;
        $classname_formprocessor_delete = $this->classname_formprocessor_delete;


        // Inject a new instance of the container, in order to inject the relevant model.
        $this->app = new Container;


        // bind the Form Processor class to the IoC (that is, inject the Form Processor class into the IoC)
        $this->$classname_formprocessor_delete = $this->app->make($namespace_formprocessor . "\\" . $classname_formprocessor_delete);


        // Now onto the Form Processor, aka "The Command Handler"
        return $this->$classname_formprocessor_delete->quarterback($this->id);
    }
}

