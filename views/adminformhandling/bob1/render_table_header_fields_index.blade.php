<thead>
    <tr class="info">

        @foreach ($field_list as $field)

            @if ( !$field['index_skip'] )
                <th style="text-align: center;">{!! $HTMLHelper::adminFormFieldLabel($field) !!}</th>
            @endif

        @endforeach

        <th style="text-align: center;">Edit<br />{!! ucwords($HTMLHelper::pluralToSingular($model_class)) !!}</th>
        <th style="text-align: center;">Delete<br />{!! ucwords($HTMLHelper::pluralToSingular($model_class)) !!}</th>

            {{-- FOR POSTS ONLY: THE POSTUPDATE BUTTON! --}}
            @if ( $table_name == "posts" )
                <th style="text-align: center;">Add a New Update</th>
            @endif

    </tr>

</thead>

