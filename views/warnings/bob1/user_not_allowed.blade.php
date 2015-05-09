@extends('lasallecmsadmin::bob1.layouts.default')

@section('content')

    <!-- Start: Main content -->
    <section class="content">

        <div class="container">

            <div class="row">

                <div class="col-md-3"></div>

                <div class="col-md-6">

                    <br /><br />
                    {!! $HTMLHelper::adminPageTitle($package_title, $table_type_plural, '') !!}


                    @include('lasallecmsadmin::bob1.partials.message')

                </div> <!-- col-md-6 -->

                <div class="col-md-3"></div>

            </div> <!-- row -->

        </div> <!-- container -->

        </div> <!-- content -->
        <!-- End: Main content -->

    </section>

@stop