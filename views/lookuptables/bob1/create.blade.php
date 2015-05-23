@extends('lasallecmsadmin::bob1.layouts.default')

@section('content')

    <!-- Main content -->
    <section class="content">

        <div class="container">

            {{-- form's title --}}
            <div class="row">
                {!! $HTMLHelper::adminPageTitle($package_title, $table_type_plural, 'Lookup Table') !!}

                @if ( isset($record) )
                    {!! $HTMLHelper::adminPageSubTitle($record, $table_type_singular) !!}
                @else
                    {!! $HTMLHelper::adminPageSubTitle(null, $table_type_singular) !!}
                @endif
            </div>

            <br /><br />

            <div class="row">

                @include('lasallecmsadmin::bob1.partials.message')

                <div class="col-md-3"></div>

                <div class="col-md-6">

                    {{-- this is a combo create or edit form. Display the proper "form action"  --}}
                    @if ( isset($record) )
                        {!! Form::model($record, array('route' => array('admin.'.$resource_route_name.'.update', $record->id), 'method' => 'PUT')) !!}

                        {!! Form::hidden('id', $record->id) !!}
                    @else
                        {!! Form::open(['route' => 'admin.'.$resource_route_name.'.store']) !!}
                    @endif

                    {{-- the table! --}}
                    <table class="table table-striped table-bordered table-condensed table-hover">
                        <tr>
                            <td>
                                {!! Form::label('name', $table_type_singular.' Name: ') !!}
                            </td>
                            <td>
                                @if ( isset($record) )
                                    {{--
                                      After trying to get $options array to work and failing, I am just using plain ol' html.
                                      Hacked Illuminate\Html\FormBuilder.php's input() method successfully, but can't seem
                                      to pass it the proper $options field. Oh well.
                                     --}}
                                    {{{ $record->title }}} &nbsp;&nbsp; <a href="#" data-toggle="popover" data-content="The name is unique, so it is unchange-able."><i class="fa fa-info-circle"></i></a>
                                    <br />
                                    {!! Form::hidden('title', $record->title) !!}
                                @else
                                    {!! Form::input('text', 'title', Input::old('title', isset($record) ? $record->title : '')) !!}
                                @endif

                            </td>
                        </tr>

                        <tr>
                            <td>
                                {!! Form::label('description', 'Description: ') !!}
                            </td>
                            <td>
                                {!! Form::input('text', 'description', Input::old('description', isset($record) ? $record->description : '')) !!}
                            </td>
                        </tr>

                        <tr>
                            <td>
                                {!! Form::label('enabled', 'Enabled: ') !!}
                            </td>
                            <td>
                                {!! Form::checkbox('enabled', '1', Input::old('enabled')) !!}
                            </td>
                        </tr>


                        @if ( isset($record) )
                            <tr>
                                <td>
                                    {!! Form::label('created at', 'Created At: ') !!}
                                </td>
                                <td>
                                    {{{ $DatesHelper::convertDatetoFormattedDateString($record->created_at) }}} &nbsp;&nbsp; <a href="#" data-toggle="popover" data-content="The Created At date is automatically filled in."><i class="fa fa-info-circle"></i></a>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    {!! Form::label('updated at', 'Updated At: ') !!}
                                </td>
                                <td>
                                    {{{ $DatesHelper::convertDatetoFormattedDateString($record->updated_at) }}} &nbsp;&nbsp; <a href="#" data-toggle="popover" data-content="The Updated At date is automatically filled in"><i class="fa fa-info-circle"></i></a>
                                </td>
                            </tr>
                        @endif


                        <tr>
                            <td>

                            </td>
                            <td>
                                @if ( isset($record) )
                                    {!! Form::submit( 'Edit '.$table_type_singular.'!') !!}
                                @else
                                    {!! Form::submit( 'Create '.$table_type_singular.'!') !!}
                                @endif

                                {!! $HTMLHelper::back_button('Cancel') !!}



                            </td>
                        </tr>

                    </table>

                    {!! Form::close() !!}


                </div> <!-- col-md-6 -->

                <div class="col-md-3"></div>

            </div> <!-- row -->


        </div> <!-- container -->

    </section>

@stop