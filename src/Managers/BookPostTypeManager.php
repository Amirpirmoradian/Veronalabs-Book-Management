<?php

namespace Piramir\BookManagement\Managers;

use Piramir\BookManagement\Models\BookInfo;

/**
 * Class BookPostTypeManager
 *
 * Handles registration of the Book custom post type, taxonomies, meta boxes,
 * saving ISBN data, and adding ISBN as a column in the admin listing.
 *
 * @package Piramir\BookManagement\Managers
 * @author  Amir Pirmoradian <piramir77@gmail.com>
 */
class BookPostTypeManager
{
    /**
     * Register hooks related to the Book post type.
     *
     * @return void
     */
    public function register(): void
    {
        add_action('init', [$this, 'registerPostType']);
        add_action('init', [$this, 'registerTaxonomies']);
        add_action('add_meta_boxes', [$this, 'addIsbnMetaBox']);
        add_action('save_post', [$this, 'saveIsbnMetaBox'], 10, 2);
        add_filter('manage_book_posts_columns', [$this, 'addIsbnColumn']);
        add_action('manage_book_posts_custom_column', [$this, 'renderIsbnColumn'], 10, 2);
    }

    /**
     * Register the Book custom post type.
     *
     * @return void
     */
    public function registerPostType(): void
    {
        $labels = [
            'name'               => __('Books', 'book-management'),
            'singular_name'      => __('Book', 'book-management'),
            'menu_name'          => __('Books', 'book-management'),
            'name_admin_bar'     => __('Book', 'book-management'),
            'add_new'            => __('Add New', 'book-management'),
            'add_new_item'       => __('Add New Book', 'book-management'),
            'new_item'           => __('New Book', 'book-management'),
            'edit_item'          => __('Edit Book', 'book-management'),
            'view_item'          => __('View Book', 'book-management'),
            'all_items'          => __('All Books', 'book-management'),
            'search_items'       => __('Search Books', 'book-management'),
            'not_found'          => __('No books found.', 'book-management'),
            'not_found_in_trash' => __('No books found in Trash.', 'book-management'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'has_archive'        => true,
            'rewrite'            => ['slug' => 'books'],
            'show_in_rest'       => true,
            'supports'           => ['title', 'editor', 'thumbnail'],
        ];

        register_post_type('book', $args);
    }

    /**
     * Register Publisher and Author taxonomies for Book post type.
     *
     * @return void
     */
    public function registerTaxonomies(): void
    {
        register_taxonomy('publisher', ['book'], [
            'label'        => __('Publishers', 'book-management'),
            'rewrite'      => ['slug' => 'publisher'],
            'hierarchical' => true,
            'show_in_rest' => true,
        ]);

        register_taxonomy('author', ['book'], [
            'label'        => __('Authors', 'book-management'),
            'rewrite'      => ['slug' => 'author'],
            'hierarchical' => false,
            'show_in_rest' => true,
        ]);
    }

    /**
     * Add the ISBN meta box to the Book post type edit screen.
     *
     * @return void
     */
    public function addIsbnMetaBox(): void
    {
        add_meta_box(
            'isbn_meta_box',
            __('ISBN', 'book-management'),
            [$this, 'renderIsbnMetaBox'],
            'book',
            'side',
            'default'
        );
    }

    /**
     * Render the ISBN meta box.
     *
     * @param \WP_Post $post WordPress post object.
     * @return void
     */
    public function renderIsbnMetaBox($post): void
    {
        $isbn = BookInfo::query()->where('post_id', $post->ID)->value('isbn');
        wp_nonce_field('save_book_isbn', 'book_isbn_nonce');

        echo '<label for="book_isbn">' . esc_html__('ISBN:', 'book-management') . '</label>';
        echo '<input type="text" id="book_isbn" name="book_isbn" value="' . esc_attr($isbn) . '" style="width:100%;" />';
    }

    /**
     * Save ISBN meta data when a Book post is saved.
     *
     * @param int     $post_id WordPress post ID.
     * @param \WP_Post $post WordPress post object.
     * @return void
     */
    public function saveIsbnMetaBox($post_id, $post): void
    {
        if ($post->post_type !== 'book') {
            return;
        }

        if (!isset($_POST['book_isbn_nonce']) || !wp_verify_nonce($_POST['book_isbn_nonce'], 'save_book_isbn')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (!isset($_POST['book_isbn'])) {
            return;
        }

        $isbn = sanitize_text_field($_POST['book_isbn']);

        if ($isbn == '') {
            return;
        }

        $existing = BookInfo::query()->where('post_id', $post_id)->exists();

        if ($existing) {
            BookInfo::query()->where('post_id', $post_id)->update(['isbn' => $isbn]);
        } else {
            BookInfo::query()->insert(['post_id' => $post_id, 'isbn' => $isbn]);
        }
    }

    /**
     * Add ISBN column to the Books admin list table after the Title column.
     *
     * @param array $columns Existing admin columns.
     * @return array Modified admin columns.
     */
    public function addIsbnColumn($columns): array
    {
        $new_columns = [];

        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;

            if ($key === 'title') {
                $new_columns['isbn'] = __('ISBN', 'book-management');
            }
        }

        return $new_columns;
    }

    /**
     * Render the ISBN value inside the custom admin column.
     *
     * @param string $column Column name.
     * @param int    $post_id WordPress post ID.
     * @return void
     */
    public function renderIsbnColumn($column, $post_id): void
    {
        if ($column === 'isbn') {
            $isbn = BookInfo::query()->where('post_id', $post_id)->value('isbn');
            echo esc_html($isbn ?: '-');
        }
    }
}
