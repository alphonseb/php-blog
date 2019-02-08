<?php
require 'database.php';
require 'functions.php';

if (!empty($_GET['forward'])) {
    $pdo->exec('
        UPDATE
            ' . $table_prefix . 'posts
        SET
            forward = 0
    ');

    $prepare = $pdo->prepare('
        UPDATE
            ' . $table_prefix . 'posts
        SET
            forward = 1
        WHERE
            id = :post_id
    ');

    $prepare->bindValue('post_id', $_GET['forward']);
    $prepare->execute();

}

if (!empty($_GET['published_id'])) {
    $prepare = $pdo->prepare('
        UPDATE
            ' . $table_prefix . 'comments
        SET
            published = 1
        WHERE
            id = :comment_id
    ');
    $prepare->bindValue('comment_id', $_GET['published_id']);
    $prepare->execute();

}

$posts_query = $pdo->query('SELECT * from ' . $table_prefix . 'posts ORDER BY publish_date DESC');
$posts       = $posts_query->fetchAll();
$forward     = null;
foreach ($posts as $post) {
    if ($post->forward == 1) {
        $forward = $post->id;
        break;
    }
}

$comments_query = $pdo->query('SELECT * from ' . $table_prefix . 'comments WHERE published = 0 ORDER BY publish_date DESC');
$comments       = $comments_query->fetchAll();

$page_title = 'Mon Blog - Espace Admin';

?>

<?php require 'header.php'; ?>
<div class="container">
    <div class="first-post">
        <h2>Article à la une</h2>
        <?php if ($forward !== null): ?>
            <p>Actuellement à la une : <?=get_post_title($forward) ?></p>
        <?php else: ?>
            <p>Pas d'articles actuellement à la une. </p>
        <?php endif; ?>
        <form action="#" method="get">
            <label for="forward">Sélectionner un article à mettre à la une :</label>
            <select name="forward" id="forward">
                <option value="">Sélectionner</option>
                <?php foreach ($posts as $post): ?>
                    <option value="<?=$post->id ?>"><?=$post->title ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Confirmer">
        </form>
    </div>
    <div class="posts">
        <h2>Mes articles</h2>
        <a href="article-edit.php">Ajouter un article</a>
        <ul>
            <?php foreach ($posts as $post): ?>
                <li>
                    <a href="article-edit.php?id=<?=$post->id ?>"><?=$post->title ?></a>
                    <span class="date"><?=$post->publish_date ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="comments">
        <h2>Commentaires à approuver</h2>
        <?php if (!empty($comments)): ?>
            <ul>
                <?php foreach ($comments as $comment): ?>
                    <li>
                        <p><?=$comment->content ?></p>
                        <p>Posté sur l'article : <?=get_post_title($comment->post_id) ?></p>
                        <p>Par : <?=$comment->author_id ?></p>
                        <a href="?published_id=<?=$comment->id ?>">Approuver</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Pas de nouveaux commentaires.</p>
        <?php endif; ?>
    </div>
</div>