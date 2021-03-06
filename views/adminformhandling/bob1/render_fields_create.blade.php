{{--

Welcome to the field rendering blade page for the create/edit admin form.

The idea is to list fields in the controller, then pass this list to the view, and then end up here for rendering. Well, it's not really "rendering", it's just a bunch of if statements. But, all the same, it means that the create/edit forms are done automatically.

There are standard fields that I am using, that make this automation a bit easier:

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

`email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
`comments` text COLLATE utf8_unicode_ci NOT NULL,
`publish_on` date NOT NULL,



--}}

{{-- THIS FILE FOR CREATE ADMIN FORMS ONLY --}}

@foreach ($field_list as $field)

    {{-- NO Primary ID form field in the Create form --}}
    @if ( $field['type'] == "int" )

        @if ( strtolower($field['name']) != "id")
            <tr>
                <td>
                    {!! Form::label($field['name'], $HTMLHelper::adminFormFieldLabel($field) .': ') !!}
                </td>
                <td>
                    {!! Form::input('number', $field['name'], Input::old($field['name'], ''), ['size' => $admin_size_input_text_box]) !!}

                    @include('formhandling::adminformhandling.bob1.popover')
                </td>
            </tr>
        @endif

    @endif


    @if (
        ($field['type'] == "varchar")               &&
        ($field['name'] != "featured_image")        &&
        ($field['name'] != "featured_image_url")    &&
        ($field['name'] != "featured_image_upload") &&
        ($field['name'] != "featured_image_server")
    )
        <tr>
            <td>
                {!! Form::label($field['name'], $HTMLHelper::adminFormFieldLabel($field) .': ') !!}
            </td>
            <td>
                {!! Form::input('text', $field['name'], Input::old($field['name'], ''), ['size' => $admin_size_input_text_box]) !!}

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif


    @if ( $field['name'] == "featured_image" )
        @include('formhandling::adminformhandling.bob1.featured_image_create')
    @endif


    @if ( ($field['type'] == "boolean") && ($field['name'] != "postupdate") )
        <tr>
            <td>
                {!! Form::label($field['name'], $HTMLHelper::adminFormFieldLabel($field) .': ') !!}
            </td>
            <td>
                {!! Form::checkbox($field['name'], '1', Input::old($field['name'])) !!}

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif


    @if ( $field['type'] == "text-with-editor" )
        <tr>
            <td>
                {!! Form::label($field['name'], $HTMLHelper::adminFormFieldLabel($field) .': ') !!}
            </td>
            <td>
                <textarea name="{!! $field['name'] !!}" id="{!! $field['name'] !!}1">
                    {!! Input::old($field['name'], '')  !!}
                </textarea>

                <script type="text/javascript" src="{{{ Config::get('app.url') }}}/packages/lasallecmsadmin/bob1/ckeditor/ckeditor.js"></script>

                {!! "<script>CKEDITOR.replace('".$field['name']."');</script>" !!}


                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif


    @if ( $field['type'] == "text-no-editor" )
        <tr>
            <td>
                {!! Form::label($field['name'], $HTMLHelper::adminFormFieldLabel($field) .': ') !!}
            </td>
            <td>
                <textarea name="{!! $field['name'] !!}" id="{!! $field['name'] !!}">{!! Input::old($field['name'], '')  !!}</textarea>

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif


    {{-- $table->date('publish_on'); --}}
    @if ( $field['type'] == "date" )
        <tr>
            <td>
                {!! Form::label($field['name'], $HTMLHelper::adminFormFieldLabel($field) .': ') !!}
            </td>
            <td>
                {!! Form::input('date', $field['name'], Input::old('publish_on', $DatesHelper::todaysDateNoTime()  )) !!}

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif


    {{-- RELATIONSHIP / LOOKUP TABLE SELECTS  --}}
    @if ( ($field['type'] == "related_table") && ($field['name'] != "post_id") )
        <tr>
            <td>
                {!! Form::label($field['name'], $HTMLHelper::adminFormFieldLabel($field) .': ') !!}
            </td>
            <td>
                {!! $repository->determineSelectFormFieldToRenderFromRelatedTable($field, 'create') !!}

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif

    {{-- FOR POST UPDATES ONLY  --}}
    @if ( ($field['type'] == "related_table") && ($field['name'] == "post_id") )
        <tr>
            <td>
                <strong>{!! $HTMLHelper::adminFormFieldLabel($field) !!}:</strong>
            </td>
            <td>
                <strong>
                    {!! $HTMLHelper::getTitleById($field['related_table_name'], $post_id)  !!}
                </strong>

                <input name="post_id" type="hidden" value="{{{ $post_id }}}">

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif


    @if ( $field['type'] == "email" )
        <tr>
            <td>
                {!! Form::label($field['name'], $HTMLHelper::adminFormFieldLabel($field) .': ') !!}
            </td>
            <td>
                {!! Form::email($field['name'], Input::old($field['name'],''), ['size' => $admin_size_input_text_box]) !!}

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif

    @if ( $field['type'] == "password" )
        <tr>
            <td>
                {!! Form::label($field['name'], $HTMLHelper::adminFormFieldLabel($field) .': ') !!}
            </td>
            <td>
                {!! Form::password($field['name'], ['size' => $admin_size_input_text_box]) !!}

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif



    @if ( ($field['type'] == "boolean") && ($field['name'] == "postupdate") )
        <input name="postupdate" type="hidden" value="false">
    @endif


@endforeach

