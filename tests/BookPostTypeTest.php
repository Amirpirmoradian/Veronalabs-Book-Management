<?php

use PHPUnit\Framework\TestCase;

/**
 * Test class for BookPostTypeManager functionality.
 */
class BookPostTypeTest extends WP_UnitTestCase
{
    /**
     * Setup before each test.
     */
    public function setUp(): void
    {
        parent::setUp();
        (new \Piramir\BookManagement\Managers\BookPostTypeManager())->register();
        flush_rewrite_rules();
    }

    /**
     * Test if the Book post type is registered.
     */
    public function test_book_post_type_registered()
    {
        $this->assertTrue(post_type_exists('book'), 'The "book" post type is not registered.');
    }

    /**
     * Test if the Publisher taxonomy is registered.
     */
    public function test_publisher_taxonomy_registered()
    {
        $this->assertTrue(taxonomy_exists('publisher'), 'The "publisher" taxonomy is not registered.');
    }

    /**
     * Test if the Author taxonomy is registered.
     */
    public function test_author_taxonomy_registered()
    {
        $this->assertTrue(taxonomy_exists('author'), 'The "author" taxonomy is not registered.');
    }
}
