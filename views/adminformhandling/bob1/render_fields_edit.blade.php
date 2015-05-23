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

{{-- THIS FILE FOR EDIT ADMIN FORMS ONLY --}}

@foreach ($field_list as $field)

    @if ( $field['type'] == "int" )
        <tr>
            <td>
                @include('formhandling::adminformhandling.bob1.form-label')
            </td>
            <td>
                @if ( $field['name'] == "id" )
                    {!! $record->id !!}
                    {!! Form::hidden('id', $record->id) !!}
                @else
                    {!! Form::input('number', $field['name'], Input::old($field['name'], $record->$field['name'])) !!}
                @endif

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif


    @if ( $field['type'] == "varchar" )
        <tr>
            <td>
                @include('formhandling::adminformhandling.bob1.form-label')
            </td>
            <td>
                {!! Form::input('text', $field['name'], Input::old($field['name'], $record->$field['name'])) !!}

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif


    @if ( $field['type'] == "boolean" )
        <tr>
            <td>
                @include('formhandling::adminformhandling.bob1.form-label')
            </td>
            <td>
                {!! Form::checkbox($field['name'], '1', Input::old($field['name'],  $record->$field['name'])) !!}

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif


    @if ( $field['type'] == "text-with-editor" )
        <tr>
            <td>
                @include('formhandling::adminformhandling.bob1.form-label')
            </td>
            <td>
                <textarea name="{!! $field['name'] !!}" id="{!! $field['name'] !!}1">
                    {!! Input::old($field['name'], $record->$field['name'])  !!}
                </textarea>

                <script type="text/javascript" src="{{{ Config::get('app.url') }}}/{{{ Config::get('lasallecms.public_folder') }}}/packages/lasallecmsadmin/bob1/ckeditor/ckeditor.js"></script>

                {!! "<script>CKEDITOR.replace('".$field['name']."');</script>" !!}


                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif


    @if ( $field['type'] == "text-no-editor" )
        <tr>
            <td>
                @include('formhandling::adminformhandling.bob1.form-label')
            </td>
            <td>
                <textarea name="{!! $field['name'] !!}" id="{!! $field['name'] !!}">{!! Input::old('excerpt', $record->$field['name'])  !!}</textarea>

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif


    {{-- $table->date('publish_on'); --}}
    @if ( $field['type'] == "date")
        <tr>
            <td>
                @include('formhandling::adminformhandling.bob1.form-label')
            </td>
            <td>
                {!! Form::input('date', $field['name'], Input::old($field['name'], $record->$field['name'])) !!}

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif


    {{-- RELATIONSHIP / LOOKUP TABLE SELECTS  --}}
    @if ( ($field['type'] == "related_table") && ($field['name'] != "post_id") )
        <tr>
            <td>
                {!! Form::label($field['name'], $HTMLHelper::adminFormFieldLabel($field['name']) .': ') !!}
            </td>
            <td>
                {!! $repository->multipleSelectFromRelatedTableEdit($field['related_table_name'], $field['related_model_class'], $record->id) !!}

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif

    {{-- FOR POST UPDATES ONLY  --}}
    @if ( ($field['type'] == "related_table") && ($field['name'] == "post_id") )
        <tr>
            <td>
                <strong>{!! $HTMLHelper::adminFormFieldLabel($field['name']) !!}:</strong>
            </td>
            <td>
                <strong>
                    ({{{ ($record->post_id) }}})  {!! $HTMLHelper::getTitleById($field['related_table_name'], $record->post_id)  !!}
                </strong>

                <input name="post_id" type="hidden" value="{{{ $record->post_id }}}">

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif

    @if ( $field['type'] == "email")
        <tr>
            <td>
                @include('formhandling::adminformhandling.bob1.form-label')
            </td>
            <td>
                {!! Form::email($field['name'], Input::old($field['name'],$record->$field['name'])) !!}

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif

    @if ( $field['type'] == "password")
        <tr>
            <td>
                @include('formhandling::adminformhandling.bob1.form-label')
            </td>
            <td>
                {!! Form::password($field['name']) !!}

                @include('formhandling::adminformhandling.bob1.popover')
            </td>
        </tr>
    @endif

@endforeach

<tr>
    <td>
        {!! Form::label('created at', 'Created At: ') !!}
    </td>
    <td>
        {{{ $DatesHelper::convertDatetoFormattedDateString($record->created_at) }}} &nbsp;&nbsp; <a tabindex="0" data-toggle="popover" data-trigger="focus" data-content="The Created At date is automatically filled in."><i class="fa fa-info-circle"></i> </a>
    </td>
</tr>

<tr>
    <td>
        {!! Form::label('updated at', 'Updated At: ') !!}
    </td>
    <td>
        {{{ $DatesHelper::convertDatetoFormattedDateString($record->updated_at) }}} &nbsp;&nbsp; <a tabindex="0" data-toggle="popover" data-trigger="focus" data-content="The Updated At date is automatically filled in."><i class="fa fa-info-circle"></i> </a>
    </td>
</tr>
