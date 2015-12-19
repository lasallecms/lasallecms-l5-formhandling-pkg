# Featured Image

A featured image is:
* one single image
* the filename (or URL) is stored in the database
* displays in specific spots in the front-end

A featured image can come from different places:
* you can upload an image from your local computer
* you can type in a URL where the image resides (eg, a CDN or AWS S3)
* you can select an existing image that resides on the server

---

When specifying Featured Image in your model, do this when specifying your fields as a property:

<pre>
// Start: Featured Image
// https://github.com/lasallecms/lasallecms-l5-formhandling-pkg/tree/master/views/adminformhandling/bob1/README_FEATURED_IMAGE.md
[
    'name'                  => 'featured_image',
    'type'                  => 'varchar',
    'info'                  => 'The one single image that represents this post, displayed in lists, and at top of the post.',
    'index_skip'            => false,
],
[
    'name'                  => 'featured_image_url',
    'type'                  => 'varchar',
    'info'                  => '',
    'index_skip'            => true,
],
[
    'name'                  => 'featured_image_upload',
    'type'                  => 'file',
    'info'                  => '',
    'index_skip'            => true,
],
[
    'name'                  => 'featured_image_server',
    'type'                  => 'varchar',
    'info'                  => '',
    'index_skip'            => true,
],
// End: Featured Image
</pre>


Or, like this when specifying your fields with a method: 

<pre>
// Start: Featured Image
// https://github.com/lasallecms/lasallecms-l5-formhandling-pkg/tree/master/views/adminformhandling/bob1/README_FEATURED_IMAGE.md
$field_list[] = [
        'name'                  => 'featured_image',
        'type'                  => 'varchar',
        'info'                  => 'The one single image that represents this post, displayed in lists, and at top of the post.',
        'index_skip'            => true,
];

$field_list[] = [
        'name'                  => 'featured_image_url',
        'type'                  => 'varchar',
        'info'                  => '',
        'index_skip'            => true,
];

$field_list[] = [
        'name'                  => 'featured_image_server',
        'type'                  => 'varchar',
        'info'                  => '',
        'index_skip'            => true,
];

$field_list[] = [
        'name'                  => 'featured_image_upload',
        'type'                  => 'file',
        'info'                  => '',
        'index_skip'            => true,
];
// End: Featured Image
        
</pre>
        
---

## IT IS IMPERATIVE THAT YOU HANDLE AN EXTERNAL IMAGE URL IN YOUR APP's BLADE VIEWS.  
## THERE IS NO MECHANISM WITHIN LASALLE SOFTWARE TO HANDLE THE DISPLAY OF EXTERNAL IMAGE URLs. 

---

featured_image        = tells the admin form automation to set up its featured image section

featured_image_url    = the image's external URL
    
featured_image_upload = upload a local image file to the server, and use this image for the featured image 

featured_image_server = browse the server for an existing image file, and use this image for the featured image

---

The "server browse" feature is not done yet. https://github.com/bestmomo/filemanager (https://github.com/simogeo/Filemanager) and https://github.com/Studio-42/elFinder are being considered, but hoping a custom small scale solution will do the trick. 

---

If multiple featured images are specified, then which field takes precedence?
* if the featured_image_url is specified, then it is used. 
* if featured_image_url is blank, and featured_image_upload is specified, then "upload" is used.
* if all fields are blank, except featured_image_server is specified, then "server" is used.

---