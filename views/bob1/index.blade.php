@extends('lasallecmsadmin::bob1.layouts.default')

@section('content')

    <!-- Start: Main content -->
    <section class="content">


        <div class="container">

            <br /><br />
            {!! $HTMLHelper::adminPageTitle($package_title, $table_type_plural, 'Lookup Table') !!}


            @include('lasallecmsadmin::bob1.partials.message')


            @if (count($records))

                {!! $HTMLHelper::adminCreateButton($resource_route_name, $table_type_singular, 'right') !!}



                {{-- bootstrap table tutorial http://twitterbootstrap.org/twitter-bootstrap-table-example-tutorial --}}

                {{-- http://datatables.net/manual/options --}}

                <table id="table_id" class="table table-striped table-bordered table-hover" data-order='[[ 1, "asc" ]]' data-page-length='25'>
                    <thead>
                    <tr class="info">
                        <th style="text-align: center;">ID</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th style="text-align: center;">Enabled</th>
                        <th></th>
                        <th></th>
                    </tr>

                    </thead>

                    <tbody>
                    @foreach ($records as $record)
                        <tr>
                            <td align="center">{{{ $record->id }}}</td>

                            <td>{{{ $record->title }}}</td>

                            <td>{{{ $record->description }}}</td>

                            <td align="center"> {!! $HTMLHelper::convertToCheckOrXBootstrapButtons($record->enabled) !!}</td>

                            <td align="center">
                                <a href="{{{ URL::route('admin.'.$resource_route_name.'.edit', $record->id) }}}" class="btn btn-success  btn-xs" role="button">
                                    <i class="glyphicon glyphicon-edit"></i>
                                </a>
                            </td>
                            <td align="center">

                                {{-- If there is just one address type, then suppress the delete button --}}
                                @if ( count($records) )

                                    {!! Form::open(array('url' => 'admin/'.$resource_route_name.'/' . $record->id)) !!}
                                    {!! Form::model($record, array('route' => array('admin.'.$resource_route_name.'.destroy', $record->id), 'method' => 'DELETE')) !!}

                                    <button type="submit" class="btn btn-danger btn-xs" data-confirm="Do you really want to DELETE the {!! strtoupper($record->title) !!} {!! strtolower($table_type_singular) !!}?">
                                        <i class="glyphicon glyphicon-remove"></i>
                                    </button>

                                    {!! Form::close() !!}
                                @endif
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>

            @else

                <br /><br />
                <h2>
                    There are no {{{ strtolower($table_type_plural) }}}. Go ahead, create your first {{{ strtolower($table_title_singular) }}}!
                </h2>

                <br />

                {!! $HTMLHelper::adminCreateButton($resource_route_name, $table_title_singular, 'left') !!}
            @endif

        </div> <!-- container -->

        </div> <!-- content -->
        <!-- End: Main content -->

    </section>


@stop