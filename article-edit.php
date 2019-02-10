<?php

require 'functions.php';

$messages = [
    'errors'  => [],
    'success' => [],
];

$title     = '';
$subtitle  = '';
$excerpt   = '';
$content   = '';
$image_url = '';
$published = 0;

if (!empty($_GET['id'])) {
    $post = get_post_by_id($_GET['id']);

    $title     = $post->title;
    $subtitle  = $post->subtitle;
    $excerpt   = $post->excerpt;
    $content   = $post->content;
    $image_url = $post->image_url;
    $published = $post->published;
}

// echo '<pre>';
// print_r($_FILES['post_image']);
// echo '</pre>';

if (isset($_FILES['post_image'])) {
    echo 'werein';
    /**
     * Handle file upload
     * Credit : http: //php.net/manual/fr/features.file-upload.php#114004
     */
    try {

        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (
            !isset($_FILES['post_image']['error']) ||
            is_array($_FILES['post_image']['error'])
        ) {
            throw new RuntimeException('Paramètres invalides');
        }

        // Check $_FILES['post_image']['error'] value.
        switch ($_FILES['post_image']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('Aucun fichier');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Fichier trop volumineux');
            default:
                throw new RuntimeException('Erreurs inconnues');
        }

        // You should also check filesize here.
        if ($_FILES['post_image']['size'] > 2000000) {
            throw new RuntimeException('Fichier trop volumineux');
        }

        // DO NOT TRUST $_FILES['post_image']['mime'] VALUE !!
        // Check MIME Type by yourself.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            $finfo->file($_FILES['post_image']['tmp_name']),
            array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
            ),
            true
        )) {
            throw new RuntimeException('Format de fichier invalide');
        }

        // You should name it uniquely.
        // DO NOT USE $_FILES['post_image']['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.
        if (!move_uploaded_file(
            $_FILES['post_image']['tmp_name'],
            sprintf('./uploads/%s.%s',
                $name = sha1_file($_FILES['post_image']['tmp_name']),
                $ext
            )
        )) {
            throw new RuntimeException('Impossible d\'enregistrer le fichier');
        }

        $image_url = sprintf('./uploads/%s.%s',
            $name,
            $ext
        );
        $messages['success']['file'] = 'Fichier envoyé';

    } catch (RuntimeException $e) {

        $messages['errors']['file'] = $e->getMessage();

    }

}

if (!empty($_POST)) {

    if (!empty($_POST['post_title'])) {
        $title = trim($_POST['post_title']);
    } else {
        $messages['errors']['title'] = 'Veuillez rentrer un titre';
    }
    if (!empty($_POST['post_subtitle'])) {
        $subtitle = trim($_POST['post_subtitle']);
    }
    if (!empty($_POST['post_excerpt'])) {
        $excerpt = trim($_POST['post_excerpt']);
    }
    if (!empty($_POST['post_content'])) {
        $content                        = trim($_POST['post_content']);
        $messages['success']['content'] = $content;
    }
    $args = [
        ':title'     => $title,
        ':subtitle'  => $subtitle,
        ':excerpt'   => $excerpt,
        ':content'   => $content,
        ':image_url' => $image_url,
        ':published' => 0,
    ];

    if (empty($messages['errors'])) {
        if (isset($_POST['post_create'])) {

            insert_post($args);

            header('Location: ?id=' . $pdo->lastInsertId());
        } elseif (isset($_POST['post_create_and_publish'])) {
            $args[':published'] = 1;

            insert_post($args);

            header('Location: ?id=' . $pdo->lastInsertId());
        } elseif (isset($_POST['post_update'])) {
            $args[':post_id'] = $_GET['id'];
            update_post($args);
        } elseif (isset($_POST['post_update_and_publish'])) {
            $args[':post_id']   = $_GET['id'];
            $args[':published'] = 1;
            update_post($args);
            if ($published == 0) {
                update_post_timestamp($_GET['id']);
            }
            $published = 1;
        }

    }

}

// echo '<pre>';
// print_r($messages);
// echo '</pre>';

$page_title = 'Mon Blog - Edition d\'un article';

?>


<?php require 'header.php' ?>

<form action="#" method="post" enctype="multipart/form-data">
    <div>
        <label for="title">Titre</label>
        <input type="text" name="post_title" id="title" value="<?=$title ?>">
    </div>
    <div>
        <div>
            <img src="<?=$image_url ?>" alt="">
        </div>
        <label for="image">Image</label>
        <input type="file" name="post_image" id="image">
    </div>
    <div>
        <label for="subtitle">Sous-Titre</label>
        <input type="text" name="post_subtitle" id="subtitle" value="<?=$subtitle ?>">
    </div>
    <div>
        <label for="excerpt">Chapeau</label>
        <textarea name="post_excerpt" id="excerpt" class="tinymce-noimage" cols="10" rows="30"><?=$excerpt ?></textarea>
    </div>
    <div>
        <label for="content">Contenu</label>
        <textarea name="post_content" id="content" class="tinymce" cols="10" rows="70"><?=$content ?></textarea>
    </div>
    <div>
        <?php if (isset($_GET['id'])): ?>
            <input type="submit" name="post_update" value="Sauvegarder">
            <input type="submit" name="post_update_and_publish" value="<?=$published == 1 ? 'Mettre à jour' : 'Sauvegarder et publier' ?>">
        <?php else: ?>
            <input type="submit" name="post_create" value="Créer">
            <input type="submit" name="post_create_and_publish" value="Créer et publier">
        <?php endif; ?>
    </div>
</form>

<?php require 'footer.php' ?>