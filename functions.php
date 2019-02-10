<?php

require_once 'database.php';

/**
 * Get the title of a post with the id
 * @param id : id of a post
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

/**
 * Get the post with the id
 * @param id : id of a post
 */

function get_post_by_id($id)
{
    global $pdo, $table_prefix;
    $prepare = $pdo->prepare('
        SELECT
            *
        FROM
            ' . $table_prefix . 'posts
        WHERE
            id = :post_id
    ');
    $prepare->bindValue('post_id', $id);
    $prepare->execute();
    $fetch = $prepare->fetch();
    return $fetch;
}

/**
 * Insert post
 * @param args : associative array of post parameters :title, subtitle, excerpt, content
 */
function insert_post($args)
{
    global $pdo, $table_prefix;
    $prepare = $pdo->prepare('
            INSERT INTO
                ' . $table_prefix . 'posts (title, subtitle, excerpt, content, image_url, published)
            VALUES
                (:title, :subtitle, :excerpt, :content, :image_url, :published)
        ');
    $prepare->bindValue('title', $args[':title']);
    $prepare->bindValue('subtitle', $args[':subtitle']);
    $prepare->bindValue('excerpt', $args[':excerpt']);
    $prepare->bindValue('content', $args[':content']);
    $prepare->bindValue('image_url', $args[':image_url']);
    $prepare->bindValue('published', $args[':published']);
    $execute = $prepare->execute();

    return $execute;

}

/**
 * Update post
 * @param args : associative array of post parameters :title, subtitle, excerpt, content
 */
function update_post($args)
{
    global $pdo, $table_prefix;
    $prepare = $pdo->prepare('
            UPDATE
                ' . $table_prefix . 'posts
            SET
                title = :title, subtitle = :subtitle, excerpt = :excerpt, image_url = :image_url, content = :content, published = :published, last_edit_date = CURRENT_TIMESTAMP
            WHERE
                id = :post_id
        ');
    $prepare->bindValue('title', $args[':title']);
    $prepare->bindValue('subtitle', $args[':subtitle']);
    $prepare->bindValue('excerpt', $args[':excerpt']);
    $prepare->bindValue('content', $args[':content']);
    $prepare->bindValue('image_url', $args[':image_url']);
    $prepare->bindValue('published', $args[':published']);
    $prepare->bindValue('post_id', $args[':post_id']);
    $execute = $prepare->execute();

    return $execute;

}

/**
 * Set post publish timestamp to current
 * @param post_id id of the post to be updated
 */
function update_post_timestamp($post_id)
{
    global $pdo, $table_prefix;
    $prepare = $pdo->prepare('
            UPDATE
                ' . $table_prefix . 'posts
            SET
                publish_date = CURRENT_TIMESTAMP
            WHERE
                id = :post_id
        ');
    $prepare->bindValue('post_id', $post_id);
    $execute = $prepare->execute();

    return $execute;

}
