<tr>
    <td colspan="2" style="border-bottom: 1px solid mediumpurple;"></td>
</tr>

<tr>
    <td>
        {!! Form::label('featured_image', 'Featured Image: ') !!}
    </td>
    <td>
        <br />
        Enter the full external URL where the image file resides (include "http://")
        <br />
        {!! Form::input('text', 'featured_image_url', Input::old('featured_image_url', ''), ['size' => $admin_size_input_text_box, 'id' => 'featured_image_server']) !!}
    </td>
</tr>

<tr>
    <td>

    </td>
    <td>
        <br />
        <em>or...</em>&nbsp;&nbsp;"Choose file" if you want to upload an image from your local computer
        <br />
        {!! Form::file('featured_image_upload', ['id' => 'featured_image_upload']) !!}
        <br />
    </td>
</tr>

<tr>
    <td>
    </td>
    <td>
        <em>or...</em>&nbsp;&nbsp;enter the image filename that is on the server
        <br />
        {!! Form::input('text', 'featured_image_server', Input::old('featured_image_server', ''), ['size' => $admin_size_input_text_box, 'id' => 'featured_image_server']) !!}
    </td>
</tr>

<tr>
    <td style="border-bottom: 1px solid mediumpurple;"></td>
    <td style="border-bottom: 1px solid mediumpurple;">
        @if (isset($field))
            @include('formhandling::adminformhandling.bob1.popover')
        @endif
    </td>
</tr>
