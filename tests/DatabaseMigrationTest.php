<?php

use WP_UnitTestCase;
use Piramir\BookManagement\Repositories\BookRepository; // Your repository class that does migration

/**
 * Class DatabaseMigrationTest
 *
 * Test if the database table is created properly.
 */
class DatabaseMigrationTest extends WP_UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Deactivate/Activate plugin manually
        (new BookRepository())->migrate();
    }

    public function test_books_info_table_exists(): void
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'books_info';

        $result = $wpdb->get_var($wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $table_name
        ));

        $this->assertEquals($table_name, $result, 'Table books_info should exist.');
    }

    public function test_books_info_table_columns_exist(): void
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'books_info';
        $columns = $wpdb->get_results("SHOW COLUMNS FROM {$table_name}", ARRAY_A);

        $column_names = array_column($columns, 'Field');

        $this->assertContains('ID', $column_names, 'Column ID should exist.');
        $this->assertContains('post_id', $column_names, 'Column post_id should exist.');
        $this->assertContains('isbn', $column_names, 'Column isbn should exist.');
    }
}
