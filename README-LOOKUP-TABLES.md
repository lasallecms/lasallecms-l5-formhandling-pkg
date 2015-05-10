# LOOKUP TABLES ADMIN HANDLING

Handling for Lookup Tables

## Description

Lookup tables have the same structure. So, why set up the same files over and over again every time you have a new lookup table? 

This package does most of the work so you can do minimal lookup table setup in the admin.

## Routes

First thing you should do is add the resource controller to your routes file. No special instructions, just specify the resource controller like usual. Here's an excerpt from my routes.php:

~~~
Route::group(array('prefix' => 'admin'), function()
{
    // Lookup Tables
    Route::resource('luaddresses', 'AdminLookupAddressTypesController');
    Route::resource('luemails', 'AdminLookupEmailTypesController');
    Route::resource('lusocials', 'AdminLookupSocialTypesController');
    Route::resource('lutelephones', 'AdminLookupTelephoneTypesController');
    Route::resource('luwebsites', 'AdminLookupWebsiteTypesController');
});
~~~

## Migrations

You have to set up the migrations and models. 

~~~
public function up()
{
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

## Seeds

Here is an excerpt from a seed file.

~~~
class LasallecrmapiTableSeeder extends Seeder
{

    /**
     * Run the LaSalleCRM database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // lookup_address_type table

        Lookup_address_type::create([
            'title'       => 'Work',
            'description' => 'Work',
            'enabled'     => 1,
            'created_at' => new DateTime,
            'created_by' => 1,
            'updated_at' => new DateTime,
            'updated_by' => 1,
        ]);
    }    
}        
~~~

## Models

The model class is highly structured. Most of it does not change. 

Modify as required where you see "lookup_address_type" and "addresses".

~~~
<?php
namespace Lasallecrm\Lasallecrmapi\Models;

/*
 * LOOKUP_ADDRESS_TYPES IS A LOOKUP TABLE!
 */

// LaSalle Software
use Lasallecms\Lasallecmsapi\Models\BaseModel;

// Laravel facades
use Illuminate\Support\Facades\DB;

class Lookup_address_type extends BaseModel
{
    ///////////////////////////////////////////////////////////////////
    //////////////          PROPERTIES              ///////////////////
    ///////////////////////////////////////////////////////////////////

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
     * User groups that are allowed to execute each controller action
     */
    protected $allowed_user_groups = [
        ['index'   => ['Super Administrator']],
        ['create'  => ['Super Administrator']],
        ['store'   => ['Super Administrator']],
        ['edit'    => ['Super Administrator']],
        ['update'  => ['Super Administrator']],
        ['destroy' => ['Super Administrator']],
    ];



    ///////////////////////////////////////////////////////////////////
    //////////////        RELATIONSHIPS             ///////////////////
    ///////////////////////////////////////////////////////////////////

    /*
     * One to one relationship with address table
     *
     * Method name must be the model name, *not* the table name
     *
     * @return Eloquent
     */
    public function address()
    {
        return $this->belongsTo('Lasallecrm\Lasallecrmapi\Models\Address');
    }



    ///////////////////////////////////////////////////////////////////
    ////////////        FOREIGN KEY CONSTRAINTS       /////////////////
    ///////////////////////////////////////////////////////////////////

    /*
     * Return an array of all the tables using a specified lookup table id.
     * The array is in the form ['table related to the lookup table' => 'count']
     *
     * @param   int   $id   Table ID
     * @return  array
     */
    public function foreignKeyCheck($id)
    {
        // 'related_table' is the table name
        return  [
            [ 'related_table' => 'addresses', 'count' => $this->addressesCount($id) ],
        ];
    }

    /*
     * Count of related table using lookup table.
     *
     * Method name is the table name (no techie reason, just a convention to adopt)
     *
     * @return int
     */
    public function addressesCount($id)
    {
        // I know eloquent does this, but having trouble so hand crafting using DB
        $record =  DB::table('addresses')->where('address_type_id', '=', $id)->get();
        return count($record);
    }
}
~~~


## Controller

The base controller does the heavy lifting. The lookup table's controller is very light. Just customizse the seven user defined properties.

~~~
<?php
namespace Lasallecrm\Lasallecrmadmin\Http\Controllers;

// LaSalle Software
use Lasallecms\Formhandling\Lookuptables\AdminLookupTableBaseController;
use Lasallecms\Lasallecmsapi\Repositories\BaseRepository;

/*
 * Resource controller for administration of lookup_address_types
 */
class AdminLookupAddressTypesController extends AdminLookupTableBaseController
{
    ///////////////////////////////////////////////////////////////////
    ////////////////     USER DEFINED PROPERTIES      /////////////////
    ////////////////           MODIFY THESE!          /////////////////
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

    /*
     * @param  Lasallecms\Lasallecmsapi\Repositories\BaseRepository
     * @return void
     */
    public function __construct(BaseRepository $repository)
    {
        // execute AdminLookupTableBaseController's construct method first in order to run the middleware
        parent::__construct() ;

        // Inject repository
        $this->repository = $repository;

        // Inject the relevant model into the repository
        $this->repository->injectModelIntoRepository($this->model_namespace."\\".$this->model_class);
    }
}
~~~






