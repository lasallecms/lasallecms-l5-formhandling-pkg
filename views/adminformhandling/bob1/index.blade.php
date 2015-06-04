@extends('lasallecmsadmin::bob1.layouts.default')

@section('content')

    <!-- Main content -->
    <section class="content">

        <div class="container">

            {{-- form's title --}}
            <div class="row">
                <br /><br />
                {!! $HTMLHelper::adminPageTitle($package_title, (ucwords($table_name)), '') !!}
                <br /><br />
            </div>



            <div class="row">

                @include('lasallecmsadmin::bob1.partials.message')

                <div class="col-md-1"></div>

                <div class="col-md-11">

                @if (count($records) > 0 )



                        @if ($resource_route_name == "postupdates")
                            {!! $HTMLHelper::adminIndexButton('posts', 'Go to the Posts listing to create a new post update', 'right') !!}
                        @else
                            {!! $HTMLHelper::adminCreateButton($resource_route_name, $model_class, 'right') !!}
                        @endif

                    {{-- bootstrap table tutorial http://twitterbootstrap.org/twitter-bootstrap-table-example-tutorial --}}
                    {{-- http://datatables.net/manual/options --}}

                    {{-- the table! --}}
                    <table id="table_id" class="table table-striped table-bordered table-hover" data-order='[[ 4, "desc" ]]' data-page-length='25'>

                        @include('formhandling::adminformhandling.bob1.render_table_header_fields_index')

                        @include('formhandling::adminformhandling.bob1.render_table_body_fields_index')

                     </table>

                @else
                    <br /><br />
                    <h2>
                        There are no {!! strtolower($HTMLHelper::properPlural($table_name)) !!}. Go ahead, create your first {!! strtolower($HTMLHelper::properPlural($model_class)) !!}!
                    </h2>

                    <br />

                    @if ($resource_route_name == "postupdates")
                         {!! $HTMLHelper::adminIndexButton('posts', 'Go to the Posts listing to create a new post update', 'left') !!}
                    @else
                         {!! $HTMLHelper::adminCreateButton($resource_route_name, strtolower($model_class), 'left') !!}
                    @endif

                @endif




                </div> <!-- col-md-11 -->

            </div> <!-- row -->

        </div> <!-- container -->

    </section>
@stop