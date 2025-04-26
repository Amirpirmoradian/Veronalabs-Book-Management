<?php

namespace Piramir\BookManagement\Repositories;

class BookRepository
{
    public function migrate(): void
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'books_info';
        $charsetCollate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$tableName} (
            ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            isbn VARCHAR(255) NOT NULL,
            PRIMARY KEY (ID),
            KEY post_id (post_id)
        ) {$charsetCollate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    public function uninstall(): void
    {
        global $wpdb;

        $tableName = $wpdb->prefix . 'books_info';
        $wpdb->query("DROP TABLE IF EXISTS {$tableName}");
    }
}
