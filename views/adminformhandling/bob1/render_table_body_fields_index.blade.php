<tbody>
    @foreach ($records as $record)
        <tr>

            @foreach ($field_list as $field)
                @if ( !$field['index_skip'] )


                    @if ( (empty($field['index_align'])) || ($field['index_align'] == "") || (strtolower($field['index_align']) == "left")  )
                        <td align="left">
                    @else
                        <td align="center">
                    @endif


                    @if ( $field['type'] == "int" )
                        {!! $record->$field['name'] !!}
                    @endif

                    @if ( ($field['type'] == "varchar") && ($field['name'] != "featured_image") )
                        {!! $HTMLHelper::finagleVarcharFieldTypeForIndexListing($field,$record->$field['name']) !!}
                    @endif

                    @if ( $field['name'] == "featured_image" )
                                <img src="{{{ Config::get('app.url') }}}/{{{ Config::get('lasallecmsfrontend.images_folder_uploaded') }}}/{!! $record->$field['name'] !!}" width="75" height="auto" />
                    @endif

                    @if ( $field['type'] == "boolean" )
                        {!! $HTMLHelper::convertToCheckOrXBootstrapButtons($record->$field['name']) !!}
                    @endif


                    @if ( $field['type'] == "text-with-editor" )
                        {!! $record->$field['name'] !!}
                    @endif

                    @if ( $field['type'] == "text-no-editor" )
                        {!! $record->$field['name'] !!}
                    @endif

                    @if ( $field['type'] == "date")
                        {!! $DatesHelper::convertDateONLYtoFormattedDateString($record->$field['name']) !!}
                    @endif

                    @if ( $field['type'] == "email" )
                        {!! $record->$field['name'] !!}
                    @endif

                    @if ( $field['type'] == "related_table" )

                        @if ( !empty($field['related_pivot_table']) )
                            {!! $HTMLHelper::listSingleCollectionElementOnSeparateRow($repository->getLookupTableRecordsAssociatedByParentId(strtolower($field['related_model_class']), $record->id)) !!}
                        @else
                            {!! $HTMLHelper::getTitleById($field['related_table_name'], $record->$field['name'])  !!}
                        @endif
                    @endif

                @endif
            @endforeach


            @if ($display_the_view_button)
                {{-- SHOW BUTTON --}}
                <td align="center">
                    <a href="{{{ URL::route('admin.'.$resource_route_name.'.show', $record->id) }}}" class="btn btn-success  btn-xs" role="button">
                        <i class="fa fa-eye"></i>
                    </a>
                </td>
            @endif

            {{-- EDIT BUTTON --}}
            <td align="center">
                <a href="{{{ URL::route('admin.'.$resource_route_name.'.edit', $record->id) }}}" class="btn btn-success  btn-xs" role="button">
                    <i class="glyphicon glyphicon-edit"></i>
                </a>
            </td>

            {{-- DELETE BUTTON --}}
            <td align="center">

                @if ( (count($records) == 1) && ($suppress_delete_button_when_one_record) )
                    {{-- // blank on purpose --}}
                @else
                    <form method="POST" action="{{{ Config::get('app.url') }}}/index.php/admin/{{ $resource_route_name }}/confirmDeletion/{{ $record->id }}" accept-charset="UTF-8">
                        {{{ csrf_field() }}}

                        <button type="submit" class="btn btn-danger btn-xs">
                            <i class="glyphicon glyphicon-remove"></i>
                        </button>

                    </form>


                @endif
            </td>


             {{-- FOR POSTS ONLY: THE POSTUPDATE BUTTON! --}}
             @if ( $table_name == "posts" )
                <td align="center">
                    <a href="postupdates/create?post_id={{ $record->id  }}"><i class="fa fa-pencil fa-lg"></i></a>
                </td>
             @endif

        </tr>
    @endforeach
</tbody>