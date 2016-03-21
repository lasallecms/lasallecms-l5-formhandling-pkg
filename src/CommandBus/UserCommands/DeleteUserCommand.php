<?php

namespace Lasallecms\Formhandling\CommandBus\UserCommands;

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



///////////////////////////////////////////////////////////////////
//// USER MANAGEMENT AND AUTHENTICATION IS SO BESPOKE THAT     ////
////      IT IS NOT PART OF LASALLE's FORM AUTOMATION          ////
///////////////////////////////////////////////////////////////////



// Laravel Software
use Lasallecms\Formhandling\CommandBus\UserCommands\Command;
use Lasallecms\Lasallecmsadmin\FormProcessing\Users\DeleteUserFormProcessing;

// Laravel classes
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Foundation\Bus\DispatchesJobs;


/**
 * Class DeleteUserCommand
 * @package Lasallecms\Formhandling\CommandBus\UserCommands
 */
class DeleteUserCommand extends Command implements SelfHandling
{
    use DispatchesJobs;

    public $id;


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle(DeleteUserFormProcessing $deleteUserFormProcessing) {
        return $deleteUserFormProcessing->quarterback($this);
    }
}