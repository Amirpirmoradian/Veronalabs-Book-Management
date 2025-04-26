<?php

namespace Piramir\BookManagement\Managers;

use Piramir\BookManagement\Admin\BooksInfoTable;

/**
 * Class BookAdminPageManager
 *
 * Handles the Books Info admin page and displays ISBNs.
 *
 * @package Piramir\BookManagement\Managers
 */
class BookAdminPageManager
{
    protected $table;

    /**
     * Register the admin menu.
     *
     * @return void
     */
    public function registerMenu(): void
    {
        add_menu_page(
            'Books Info',
            'Books Info',
            'manage_options',
            'books-info',
            [$this, 'renderPage'],
            'dashicons-book',
            6
        );
    }

    /**
     * Render the admin page content.
     *
     * @return void
     */
    public function renderPage(): void
    {
        if (!class_exists('WP_List_Table')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
        }

        if (!function_exists('convert_to_screen')) {
            require_once ABSPATH . 'wp-admin/includes/screen.php';
        }

        if (!$this->table) {
            $this->table = new BooksInfoTable();
        }

        echo '<div class="wrap">';
        echo '<h1 class="wp-heading-inline">Books Info</h1>';

        $this->table->prepare_items();

        echo '<form method="post">';
        $this->table->display();
        echo '</form>';
        echo '</div>';
    }
}
