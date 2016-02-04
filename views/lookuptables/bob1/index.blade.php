@extends('lasallecmsadmin::bob1.layouts.default')

@section('content')

    <!-- Main content -->
    <section class="content">

        <div class="container">

            {{-- form's title --}}
            <div class="row">
                <br /><br />
                {!! $HTMLHelper::adminPageTitle($package_title, $table_type_plural, 'Lookup Table') !!}
                <br /><br />
            </div>



            <div class="row">

                @include('lasallecmsadmin::bob1.partials.message')

                <div class="col-md-1"></div>

                <div class="col-md-11">


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
                        <th style="text-align: center;">Edit</th>
                        <th style="text-align: center;">Delete</th>
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
                                    <i class="fa fa-edit"></i>
                                </a>
                            </td>
                            <td align="center">

                                @if ( (count($records) == 1) && ($suppress_delete_button_when_one_record) )
                                    {{-- // blank on purpose --}}
                                @else
                                    <form method="POST" action="{{{ Config::get('app.url') }}}/index.php/admin/{{ $resource_route_name }}/confirmDeletion/{{ $record->id }}" accept-charset="UTF-8">
                                    {{{ csrf_field() }}}

                                    <button type="submit" class="btn btn-danger btn-xs">
                                         <i class="fa fa-times"></i>
                                    </button>

                    </form>

                                @endif
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>

            @else

                <br /><br />
                <h2>
                    There are no {{{ strtolower($table_type_plural) }}}. Go ahead, create your first {{{ strtolower($table_type_singular) }}}!
                </h2>

                <br />

                {!! $HTMLHelper::adminCreateButton($resource_route_name, $table_type_singular, 'left') !!}
            @endif

                </div> <!-- col-md-11 -->

            </div> <!-- row -->

        </div> <!-- container -->

    </section>


@stop