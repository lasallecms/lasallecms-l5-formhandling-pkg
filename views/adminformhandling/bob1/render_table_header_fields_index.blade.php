<thead>
    <tr class="info">

        @foreach ($field_list as $field)

            {{-- The checkbox --> the primary ID field is always the first field. --}}
            @if ($field['name'] == 'id')
                <th style="text-align: center;"></th>
            @endif

            @if ( !$field['index_skip'] )
                <th style="text-align: center;">{!! $HTMLHelper::adminFormFieldLabel($field) !!}</th>
            @endif

        @endforeach

        @if ($display_the_view_button)
            <th style="text-align: center;">View<br />{!! ucwords($HTMLHelper::pluralToSingular($model_class)) !!}</th>
        @endif

        <th style="text-align: center;">Edit<br />{!! ucwords($HTMLHelper::pluralToSingular($model_class)) !!}</th>
        <th style="text-align: center;">Delete<br />{!! ucwords($HTMLHelper::pluralToSingular($model_class)) !!}</th>

        {{-- FOR POSTS ONLY: THE POSTUPDATE BUTTON! --}}
        @if ( $table_name == "posts" )
            <th style="text-align: center;">Add a New Update</th>
        @endif

    </tr>

</thead>

