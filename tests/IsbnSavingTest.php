<?php

use WP_UnitTestCase;
use Piramir\BookManagement\Models\BookInfo;

/**
 * Class IsbnSavingTest
 *
 * Test saving ISBN to database.
 */
class IsbnSavingTest extends WP_UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_isbn_saved_on_book_creation(): void
    {
        // Create a new book post
        $post_id = wp_insert_post([
            'post_title'   => 'Test Book',
            'post_type'    => 'book',
            'post_status'  => 'publish',
        ]);

        // Simulate saving ISBN
        $_POST['book_isbn'] = '978-3-16-148410-0';
        $_POST['book_isbn_nonce'] = wp_create_nonce('save_book_isbn');

        $post = get_post($post_id);

        (new Piramir\BookManagement\Managers\BookPostTypeManager())->saveIsbnMetaBox($post_id, $post);

        // Check if ISBN is saved in books_info table
        $saved_isbn = BookInfo::query()->where('post_id', $post_id)->value('isbn');

        $this->assertEquals('978-3-16-148410-0', $saved_isbn, 'ISBN should be saved properly.');
    }
}
