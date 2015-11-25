@extends('lasallecmsadmin::bob1.layouts.default')

@section('content')

    <!-- Main content -->
    <section class="content">

        <div class="container">

            {{-- form's title --}}
            <div class="row">
                <br /><br />
                {!! $HTMLHelper::adminPageTitle($package_title, (ucwords($table_name)), '') !!}
                {!! $HTMLHelper::adminPageSubTitle($record, $model_class) !!}
                <br /><br />
            </div>

            <div class="row">

                @include('lasallecmsadmin::bob1.partials.message')

                <div class="col-md-3"></div>

                <div class="col-md-9">

                    {!! Form::model($record, array('route' => array('admin.'.$resource_route_name.'.update', $record->id), 'method' => 'PUT')) !!}

                    {{-- the table! --}}
                    <table class="table table-striped table-bordered table-condensed table-hover">

                        <tr>
                            <td>

                            </td>
                            <td align="right">
                                {{-- Submit and cancel buttons --}}
                                {!! Form::submit( 'Save & Exit' ) !!}
                                {!! Form::submit( 'Save & Edit', ['name' => 'return_to_edit'] ) !!}
                                {!! $HTMLHelper::back_button('Cancel') !!}
                            </td>
                        </tr>

                        <tr><td colspan="2"></td></tr>

                        @include('formhandling::adminformhandling.bob1.render_fields_edit')

                        <tr><td colspan="2"></td></tr>

                        <tr>
                            <td>

                            </td>
                            <td>
                                {{-- Hidden fields --}}
                                {!! Form::hidden('id', $record->id) !!}
                                <input name="field_list" type="hidden" value="{{{ json_encode($field_list) }}}">
                                <input name="namespace_formprocessor" type="hidden" value="{{{ $namespace_formprocessor  }}}">
                                <input name="classname_formprocessor_update" type="hidden" value="{{{ $classname_formprocessor_update }}}">
                                <input name="crud_action" type="hidden" value="update">

                                {{-- Submit and cancel buttons --}}
                                {!! Form::submit( 'Save & Exit' ) !!}
                                {!! Form::submit( 'Save & Edit', ['name' => 'return_to_edit'] ) !!}
                                {!! $HTMLHelper::back_button('Cancel') !!}
                            </td>
                        </tr>

                    </table>

                    {!! Form::close() !!}

                </div> <!-- col-md-9 -->



            </div> <!-- row -->


        </div> <!-- container -->

    </section>
@stop