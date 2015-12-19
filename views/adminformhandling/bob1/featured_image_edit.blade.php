<tr>
    <td colspan="2" style="border-bottom: 1px solid mediumpurple;"></td>
</tr>


@if ($record->featured_image)
    <tr>
        <td>
            {!! Form::label($field['name'], $HTMLHelper::adminFormFieldLabel($field) .': ') !!}
        </td>
        <td>
            Your featured image is currently:
            <br />
            <strong>{!! $record->featured_image !!}</strong>
            <br /><br />

            @if ($HTMLHelper::isHTTPorHTTPS($record->featured_image))
                <img src="{!! $record->featured_image !!}" width="150" height="auto" />
            @else
                <img src="{{{ Config::get('app.url') }}}/{{{ Config::get('lasallecmsfrontend.images_folder_uploaded') }}}/{!! $record->featured_image !!}" width="150" height="auto" />
            @endif

            {!! Form::hidden('featured_image', $record->featured_image) !!}

        </td>
    </tr>
@endif

<tr>
    <td>
    </td>
    <td>
        <br />
        Enter the full external URL where the image file resides (include "http://")
        <br />
        {!! Form::input('text', 'featured_image_url', Input::old('featured_image_url', $HTMLHelper::isHTTPorHTTPS($record->featured_image) ?  $record->featured_image : ''), ['size' => $admin_size_input_text_box]) !!}
    </td>
</tr>

<tr>
    <td>

    </td>
    <td>
        <em>or...</em>&nbsp;&nbsp;"Choose file" if you want to upload an image from your local computer
        <br />
        {!! Form::file('featured_image_upload', null) !!}
        <br />
    </td>
</tr>

<tr>
    <td>
    </td>
    <td>
        <em>or...</em>&nbsp;&nbsp;enter the image filename that is on the server
        <br />
        {!! Form::input('text', 'featured_image_server', Input::old('featured_image_server', $HTMLHelper::isHTTPorHTTPS($record->featured_image) ?  '' : $record->featured_image), ['size' => $admin_size_input_text_box]) !!}
    </td>
</tr>

<tr>
    <td style="border-bottom: 1px solid mediumpurple;"></td>
    <td style="border-bottom: 1px solid mediumpurple;">@include('formhandling::adminformhandling.bob1.popover')</td>
</tr>
