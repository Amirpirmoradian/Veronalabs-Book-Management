<?php

use WP_UnitTestCase;
use Piramir\BookManagement\Managers\BookAdminPageManager;

/**
 * Class AdminPageTest
 *
 * Test if the admin page is registered.
 */
class AdminPageTest extends WP_UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        (new BookAdminPageManager())->registerMenu();
    }

    public function test_books_info_admin_menu_registered(): void
    {
        global $menu;
        $menu_titles = wp_list_pluck($menu, 0);

        $this->assertContains('Books Info', $menu_titles, 'Admin menu "Books Info" should be registered.');
    }
}
