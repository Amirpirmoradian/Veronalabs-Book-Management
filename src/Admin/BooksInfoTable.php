<?php

namespace Piramir\BookManagement\Admin;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
if (!function_exists('convert_to_screen')) {
    require_once ABSPATH . 'wp-admin/includes/screen.php';
}

use Piramir\BookManagement\Models\BookInfo;
use WP_List_Table;

/**
 * Class BooksInfoTable
 *
 * Display the list of books (ISBN + Post ID) in admin using WP_List_Table with pagination, sorting, search, and bulk actions.
 *
 * @package Piramir\BookManagement\Admin
 */
class BooksInfoTable extends WP_List_Table
{
    public function __construct()
    {
        parent::__construct([
            'singular' => 'bookinfo',
            'plural'   => 'bookinfos',
            'ajax'     => false,
        ]);
    }

    public function get_columns(): array
    {
        return [
            'post_id' => __('Post ID', 'book-management'),
            'isbn'    => __('ISBN', 'book-management'),
        ];
    }

    public function get_sortable_columns(): array
    {
        return [
            'post_id' => ['post_id', true],
            'isbn'    => ['isbn', false],
        ];
    }

    protected function column_post_id($item): string
    {
        $post_edit_link = get_edit_post_link($item['post_id']);
        $postTitle = get_the_title($item['post_id']);

        $actions = [
            'edit' => sprintf(
                '<a href="%s">%s</a>',
                esc_url($post_edit_link),
                __('Edit', 'book-management')
            ),
            'delete' => sprintf(
                '<a href="?page=books-info&action=delete&id=%d" onclick="return confirm(\'%s\')">%s</a>',
                $item['ID'],
                esc_js(__('Are you sure?', 'book-management')),
                __('Delete', 'book-management')
            ),
        ];

        return sprintf(
            '%1$s %2$s',
            esc_html($item['post_id'] . " ($postTitle)"),
            $this->row_actions($actions)
        );
    }

    protected function column_isbn($item): string
    {
        return esc_html($item['isbn']);
    }

    public function prepare_items(): void
    {
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];

        $per_page = 5;
        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        $orderby = $_REQUEST['orderby'] ?? 'post_id';
        $order = $_REQUEST['order'] ?? 'asc';

        $query = BookInfo::query();

        if (in_array($orderby, ['post_id', 'isbn']) && in_array(strtolower($order), ['asc', 'desc'])) {
            $query->orderBy($orderby, $order);
        }

        $total_items = $query->count();

        $data = $query
            ->offset($offset)
            ->limit($per_page)
            ->get()
            ->toArray();

        $this->items = $data;
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);
    }

    /**
     * Default column rendering.
     *
     * @param object|array $item
     * @param string $column_name
     * @return string
     */
    public function column_default($item, $column_name): string
    {
        return $item[$column_name] ?? '';
    }

    /**
     * Render the full page.
     *
     * @return void
     */
    public static function render(): void
    {
        $table = new self();
        $table->prepare_items();

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Books Info Table', 'book-management') . '</h1>';
        echo '<form method="post">';
        $table->search_box(__('Search Books', 'book-management'), 'search_id');
        echo '<input type="hidden" name="page" value="books-info">';
        $table->display();
        echo '</form>';
        echo '</div>';
    }
}
