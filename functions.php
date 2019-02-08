<?php

/**
 * Get the title of a post with the id
 * @param $id : id of a post
 */

function get_post_title($id)
{
    global $pdo, $table_prefix;
    $prepare = $pdo->prepare('
        SELECT
            title
        FROM
            ' . $table_prefix . 'posts
        WHERE
            id = :post_id
    ');
    $prepare->bindValue('post_id', $id);
    $prepare->execute();
    $fetch = $prepare->fetch();
    return $fetch->title;
}
