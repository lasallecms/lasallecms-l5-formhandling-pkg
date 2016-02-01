@extends('lasallecmsadmin::bob1.layouts.default')

@section('content')

        <!-- Main content -->
<section class="content">

    <div class="container">
        <div class="row">

            {{-- form's title --}}
            <div class="row">
                <br /><br />
                {!! $HTMLHelper::adminPageTitle($package_title, (ucwords($table_name)), '') !!}
                <br /><br />
            </div>


            <div class="row">

                <div class="col-md-2 text-center">
                    <div class="boxX">
                        <div class="box-content">
                            {{-- empty on purpose --}}
                        </div>
                    </div>
                </div>

                <div class="col-md-8 text-center">
                    <div class="box">
                        <div class="box-content">
                            <h1 class="tag-title">
                                Hey, {{ $user->name }}!
                                <hr />
                                @if (count($records) > 1)
                                    Just want to confirm these deletions with you first.
                                @else
                                    Just want to confirm this deletion with you first.
                                @endif
                                <hr />
                                Do you really want to delete

                                @if (count($records) > 1)
                                    these {{ strtolower($HTMLHelper::properPlural($model_class)) }} records?
                                @else
                                    this {{ strtolower($HTMLHelper::properPlural($model_class)) }}?
                                @endif

                            </h1>
                            <hr />
                            <p>&nbsp;</p>
                            <br />



                            <form method="POST" action="{{{ Config::get('app.url') }}}/index.php/admin/{{ $resource_route_name }}/destroyMultipleRecords" accept-charset="UTF-8">
                                {{{ csrf_field() }}}


                                <table class="table table-striped table-bordered table-hover">
                                    <thead class="success">
                                    <th style="text-align: center;"><h2></h2></th>
                                    <th style="text-align: left;"><h2>ID</h2></th>
                                    <th style="text-align: left;"><h2>Title</h2></th>
                                    </thead>

                                    <tbody>
                                    @foreach ($records as $record)
                                        <tr>
                                            <td align="center"><h2><input name="checkbox[]" type="checkbox" checked="checked" value="{!! $record->id !!}"></h2></td>
                                            <td align="left"><h2>{{{ $record->id }}}</h2></td>
                                            <td align="left"><h2>{{{ $record->title }}}</h2></td>
                                        </tr>

                                        <

                                    @endforeach
                                    </tbody>
                                </table>


                                <br /><br />


                                <button type="submit" class="btn btn-block btn-success">
                                    <h3><i class="fa fa-check fa-2x"></i>&nbsp;&nbsp; <u>Yes</u>, I am absolutely sure that I want to delete
                                        @if (count($records) > 1)
                                            these {{ strtolower($HTMLHelper::properPlural($model_class)) }} records.
                                        @else
                                            this {{ strtolower($HTMLHelper::properPlural($model_class)) }}.
                                        @endif

                                    </h3>
                                </button>


                                {!! Form::close() !!}

                                <br /><br />

                                <a href="{{{ URL::route('admin.'.$resource_route_name.'.index') }}}" class="btn btn-block btn-danger"><h3><i class="fa fa-times fa-2x"></i>&nbsp;&nbsp; Oh <u>No</u>, I do <u>not</u> want to delete

                                        @if (count($records) > 1)
                                            these {{ strtolower($HTMLHelper::properPlural($model_class)) }} records.
                                        @else
                                            this {{ strtolower($HTMLHelper::properPlural($model_class)) }}.
                                        @endif

                                    </h3></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-2 text-center">
                    <div class="boxX">
                        <div class="box-content">
                            {{-- empty on purpose --}}
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </div>


</section>
@stop