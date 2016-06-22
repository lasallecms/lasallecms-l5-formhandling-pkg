{{-- THIS FILE FOR VIEW ADMIN FORMS ONLY --}}

@foreach ($field_list as $field)

    @if ( $field['type'] == "int" )
        <tr>
            <td>
                {!! $HTMLHelper::adminFormFieldLabel($field) !!}:
            </td>
            <td>
                {{{ $record->id }}}
            </td>
        </tr>
    @endif

    @if (
            ($field['type'] == "varchar") &&
            ($field['name'] != "featured_image") &&
            ($field['name'] != "featured_image_url") &&
            ($field['name'] != "featured_image_upload") &&
            ($field['name'] != "featured_image_server")
        )
        <tr>
            <td>
                {!! $HTMLHelper::adminFormFieldLabel($field) !!}:
            </td>
            <td>
                {{{ $record->{$field['name']} }}}
            </td>
        </tr>
    @endif

    @if ($field['name'] == "featured_image")
        <td> {!! $HTMLHelper::adminFormFieldLabel($field) !!}:</td>

        @if ($record->{$field['name']})
            <td>
                <img src="{{{ Config::get('app.url') }}}/{{{ Config::get('lasallecmsfrontend.images_folder_uploaded') }}}/{!! $record->{$field['name']} !!}" width="150" height="auto" />
                <br />
                ({!! $record->{$field['name']} !!})
            </td>
        @else
            <td>
                You have <strong>not</strong> chosen a featured image.
            </td>
         @endif

    @endif


    @if ( $field['type'] == "boolean" )
        <tr>
            <td>
                {!! $HTMLHelper::adminFormFieldLabel($field) !!}:
            </td>
            <td>
                {{{ $record->{$field['name']} }}}
            </td>
        </tr>
    @endif


    {{-- This field has html that needs to render as html. --}}
    @if ( $field['type'] == "text-with-editor" )
        <tr>
            <td>
                {!! $HTMLHelper::adminFormFieldLabel($field) !!}:
            </td>
            <td>
                {{-- UNESCAPED SO THAT THE HTML RENDERS --}}
                {!! $record->{$field['name']} !!}
            </td>
        </tr>
    @endif


    @if ( $field['type'] == "text-no-editor" )
        <tr>
            <td>
                {!! $HTMLHelper::adminFormFieldLabel($field) !!}:
            </td>
            <td>
                {{{ $record->{$field['name']} }}}
            </td>
        </tr>
    @endif


    {{-- $table->date('publish_on'); --}}
    @if ( $field['type'] == "date")
        <tr>
            <td>
                {!! $HTMLHelper::adminFormFieldLabel($field) !!}:
            </td>
            <td>
                {{{ $record->{$field['name']} }}}
            </td>
        </tr>
    @endif

    @if ( ($field['type'] == "related_table") && ($field['name'] != "post_id") )
        <tr>
            <td>
                {!! $HTMLHelper::adminFormFieldLabel($field) !!}:
            </td>
            <td>


                @if ( !empty($field['related_pivot_table']) )
                    {!! $HTMLHelper::listSingleCollectionElementOnSeparateRow($repository->getLookupTableRecordsAssociatedByParentId(strtolower($field['related_model_class']), $record->id)) !!}
                @else
                    {!! $HTMLHelper::getTitleById($field['related_table_name'], $record->{$field['name']})  !!}
                @endif

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
                    {!! $HTMLHelper::getTitleById($field['related_table_name'], $record->post_id)  !!}
                </strong>

                <input name="post_id" type="hidden" value="{{{ $record->post_id }}}">
            </td>
        </tr>
    @endif

    @if ( $field['type'] == "email" )
        <tr>
            <td>
                {!! Form::label($field['name'], $HTMLHelper::adminFormFieldLabel($field) .': ') !!}
            </td>
            <td>
                {{{ $record->{$field['name']} }}}
            </td>
        </tr>
    @endif

    @if ( $field['type'] == "password")
        <tr>
            <td>
                {!!  $HTMLHelper::adminFormFieldLabel($field) !!}:
            </td>
            <td>
                {{{ $record->{$field['name']} }}}
            </td>
        </tr>
    @endif



@endforeach

<tr>
    <td>
        Created At:
    </td>
    <td>
        {!! $DatesHelper::convertDatetoFormattedDateString($record->created_at) !!}
    </td>
</tr>

<tr>
    <td>
        Updated At:
    </td>
    <td>
        {!! $DatesHelper::convertDatetoFormattedDateString($record->updated_at) !!}
    </td>
</tr>
