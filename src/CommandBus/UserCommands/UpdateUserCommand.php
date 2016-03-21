<?php

namespace Lasallecms\Formhandling\CommandBus\UserCommands;

/**
 *
 * Administrative package for the LaSalle Content Management System, based on the Laravel 5 Framework
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
 * @package    Administrative package for the LaSalle Content Management System

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
use Lasallecms\Lasallecmsadmin\FormProcessing\Users\UpdateUserFormProcessing;

// Laravel classes
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Foundation\Bus\DispatchesJobs;


class UpdateUserCommand extends Command implements SelfHandling
{
    use DispatchesJobs;

    public $id;
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $activated;
    public $enabled;
    public $groups;
    public $two_factor_auth_enabled;
    public $phone_country_code;
    public $phone_number;
    public $sms_token;
    public $sms_token_created_at;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($id, $name, $email, $password, $password_confirmation, $activated=0, $enabled=0, $groups, $two_factor_auth_enabled=0, $phone_country_code, $phone_number) {
        $this->id                    = $id;
        $this->name                  = $name;
        $this->email                 = $email;
        $this->password              = $password;
        $this->password_confirmation = $password_confirmation;
        $this->activated             = $activated;
        $this->enabled               = $enabled;
        $this->groups                = $groups;
        $this->two_factor_auth_enabled  = $two_factor_auth_enabled;
        $this->phone_country_code    = $phone_country_code;
        $this->phone_number          = $phone_number;
        $this->sms_token             = null;
        $this->sms_token_created_at  = null;
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle(UpdateUserFormProcessing $updateUserFormProcessing) {
        return $updateUserFormProcessing->quarterback($this);
    }
}