# LOOKUP TABLES ADMIN HANDLING

Handling for Lookup Tables

## Description

Lookup tables have the same structure. So, why set up the same files over and over again every time you have a new lookup table? 

This package does most of the work so you can do minimal lookup table setup in the admin.

## Migrations

You have to set up the migrations and models. 

~~~
public function up()
{
    // START: Lookup tables

    if (!Schema::hasTable('lookup_address_types'))
    {
        Schema::create('lookup_address_types', function (Blueprint $table)
        {
            $table->engine = 'InnoDB';

            $table->increments('id')->unsigned();

            $table->string('title')->unique();
            $table->string('description');

            $table->boolean('enabled')->default(true);

            $table->timestamp('created_at');
            $table->integer('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users');

            $table->timestamp('updated_at');
            $table->integer('updated_by')->unsigned();
            $table->foreign('updated_by')->references('id')->on('users');

            $table->timestamp('locked_at')->nullable();
            $table->integer('locked_by')->nullable()->unsigned();
            $table->foreign('locked_by')->references('id')->on('users');
        });
    }
}   
 
public function down()
{
    Schema::table('lookup_address_types', function($table){
        $table->dropIndex('lookup_address_types_title_unique');
        $table->dropForeign('lookup_address_types_created_by_foreign');
        $table->dropForeign('lookup_address_types_updated_by_foreign');
        $table->dropForeign('lookup_address_types_locked_by_foreign');
    });
    Schema::dropIfExists('lookup_address_types');
}         
~~~

Here is the MySQL:

~~~
CREATE TABLE IF NOT EXISTS `lookup_address_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_by` int(10) unsigned NOT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `locked_by` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lookup_address_types_title_unique` (`title`),
  KEY `lookup_address_types_created_by_foreign` (`created_by`),
  KEY `lookup_address_types_updated_by_foreign` (`updated_by`),
  KEY `lookup_address_types_locked_by_foreign` (`locked_by`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

~~~

## Models

I've always wanted to pursue a modelling career, and Laravel gives me my lifetime opportunity to actualize my dream. Oh, thank you Laravel!

Here's the model:

~~~
<?php namespace Lasallecrm\Lasallecrmapi\Models;

use Lasallecrm\Lasallecrmapi\Models\BaseModel;

class Lookup_address_type extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    public $table = 'lookup_address_types';


    /**
     * Which fields may be mass assigned
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'enabled'
    ];


    /*
     * One to one relationship with address table
     *
     * @return Eloquent
     */
    public function address()
    {
        return $this->belongsTo('Lasallecrm\Lasallecrmapi\Models\Address');
    }
}
~~~

Modify your namespace and class names, eh!

The assumption is that there is one "real" table that has a one-to-one relationship with this lookup table. In this code, it is the "address" table. 

## Controller

This package does the hard lifting. The controller you specify in your app:

~~~
<?php namespace Lasallecrm\Lasallecrmadmin\Http\Controllers;

use Lasallecms\Lookuptables\AdminLookupTableBaseController;
use Lasallecms\Lookuptables\LookupRepository;


class AdminLookupAddressTypesController extends AdminLookupTableBaseController
{

    ///////////////////////////////////////////////////////////////////
    ////////////////     USER DEFINED PROPERTIES      /////////////////
    ///////////////////////////////////////////////////////////////////

    /*
     * @var Name of this package
     */
    protected $package_title        = "Customer Management";

    /*
     * Lookup table type, in the plural
     */
    protected $table_type_plural   = "Address Types";

    /*
     * Lookup table type, in the singular
     */
    protected $table_type_singular  = "Address Type";

    /*
     * Lookup table name
     */
    protected $table_name           = "lookup_address_types";

    /*






     * This lookup table's model class namespace
     */
    protected $model_namespace      = "Lasallecrm\Lasallecrmapi\Models";

    /*
     * This lookup table's model class
     */
    protected $model_class          = "Lookup_address_type";

    /*
     * The base URL of this lookup table's resource routes
     */
    protected $resource_route_name   = "luaddresses";



    ///////////////////////////////////////////////////////////////////
    ////////////////     DO NOT MODIFY BELOW!         /////////////////
    ///////////////////////////////////////////////////////////////////

    public function __construct(LookupRepository $repository)
    {
        // execute AdminController's construct method first in order to run the middleware
        parent::__construct() ;

        // Inject repository
        $this->repository = $repository;

        // Inject the relevant model into the repository
        $this->repository->injectModelIntoRepository($this->model_namespace."\\".$this->model_class);
    }
}
~~~

Modify the namespace and class name. Then, modify the properties. 




