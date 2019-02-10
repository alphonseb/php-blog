<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?=$page_title ?></title>
    <script src='https://cloud.tinymce.com/5/tinymce.min.js?apiKey=cufj7n76c21xsctcz4wmjvd7lzwviexn88irtasdlvp3mevq'></script>
    <script>
        tinymce.init({
            selector: '.tinymce',
            images_upload_url: 'tinymce_images.php',
            plugins: "image",
            toolbar: "image"
        });
        tinymce.init({
            selector: '.tinymce-noimage',
        });
    </script>
</head>
<body>