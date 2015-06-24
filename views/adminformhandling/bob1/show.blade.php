@extends('lasallecmsadmin::bob1.layouts.default')

@section('content')

    <!-- Main content -->
    <section class="content">

        <div class="container">

            {{-- form's title --}}
            <div class="row">
                <br /><br />
                {!! $HTMLHelper::adminPageTitle($package_title, (ucwords($table_name)), '') !!}
                {!! $HTMLHelper::adminPageSubTitle($record, $model_class, true) !!}
                <br /><br />
            </div>

            <div class="row">

                @include('lasallecmsadmin::bob1.partials.message')

                <div class="col-md-3"></div>

                <div class="col-md-9">

                    {{-- the table! --}}
                    <table class="table table-striped table-bordered table-condensed table-hover">

                        @include('formhandling::adminformhandling.bob1.render_fields_show')

                    </table>



                </div> <!-- col-md-9 -->



            </div> <!-- row -->

            <div class="row">

                <br /><br />

                <div class="col-md-5"></div>

                <div class="col-md-3">

                    <a href="{{{ URL::route('admin.'.$resource_route_name.'.edit', $record->id) }}}" class="btn btn-success  btn-lg" role="button">
                        <i class="glyphicon glyphicon-edit"></i>  Edit this {!! strtolower($HTMLHelper::properPlural($model_class)) !!}
                    </a>

                    <br /><br />

                    <a href="{{{ URL::route('admin.'.$resource_route_name.'.index') }}}" class="btn btn-success  btn-lg" role="button">
                        <i class="glyphicon glyphicon-list-alt"></i>  Return to the {!! strtolower($HTMLHelper::properPlural($model_class)) !!} Listing
                    </a>


                </div>

                <div class="col-md-7"></div>


        </div> <!-- container -->

    </section>
@stop