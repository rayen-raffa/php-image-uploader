<?php
 

$target_dir = 'uploads'; // needs to be created manually, in the same directory as this file
$redirect_url = 'http://www.google.com/'; // Needs to start with http://
$max_file_size = 1000000; // Enter Image size in Bytes
$input_field_name = 'up_file'; // Needs to match the name field in the corresponding <input> tag

try {

    // Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($_FILES[$input_field_name]['error']) ||
        is_array($_FILES[$input_field_name]['error'])
    ) {
        throw new RuntimeException('Invalid parameters. Please go back and try again :)');
    }

    // Check $_FILES[$input_field_name]['error'] value.
    switch ($_FILES[$input_field_name]['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent. Please go back and try again :)');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit. Please go back and try again :)');
        default:
            throw new RuntimeException('Unknown errors. Please go back and try again :)');
    }

    // You should also check filesize here.
    if ($_FILES[$input_field_name]['size'] > $max_file_size) {
        throw new RuntimeException('Exceeded filesize limit. Please go back and try again :)');
    }

    // DO NOT TRUST $_FILES[$input_field_name]['mime'] VALUE !!
    // Check MIME Type by yourself.
    // checking for file extension and saving it in $ext
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (false === $ext = array_search(
        $finfo->file($_FILES[$input_field_name]['tmp_name']),
        array(
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
        ),
        true
    )) {
        throw new RuntimeException('Invalid file format. Please go back and try again :)');
    }

    // You should name it uniquely.
    // DO NOT USE $_FILES[$input_field_name]['name'] WITHOUT ANY VALIDATION !!
    // On this example, obtain safe unique name from its binary data.
    // TODO : change image save name to add save  date signature
    $file_name = str_replace(" ","-",$_FILES[$input_field_name]['name']);  
    $target_file = sprintf('./%s/%s_%s',$target_dir,date("YmdHms"), $file_name);
	    
    if (!move_uploaded_file($_FILES[$input_field_name]['tmp_name'], $target_file )) {
        throw new RuntimeException('Failed to move uploaded file. Please go back and try again :)');
    }

    // echo 'File is uploaded successfully.';
    header('Location: '.$redirect_url);
    die();
} catch (RuntimeException $e) {
    header('Content-Type: text/plain; charset=utf-8');
    echo $e->getMessage();

}

?>
