<?php

namespace Piramir\BookManagement\ServiceProviders;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Rabbit\Contracts\BootablePluginProviderInterface;
use Piramir\BookManagement\Managers\BookAdminPageManager;

/**
 * Class AdminPageServiceProvider
 *
 * Register and load the Books Info admin page.
 *
 * @package Piramir\BookManagement\ServiceProviders
 */
class AdminPageServiceProvider extends AbstractServiceProvider implements BootablePluginProviderInterface
{
    protected $provides = [
        'book.admin.page',
    ];

    public function register(): void
    {
        $this->getContainer()->add('book.admin.page', function () {
            return new BookAdminPageManager();
        });
    }

    public function bootPlugin(): void
    {
        $manager = $this->getContainer()->get('book.admin.page');
        add_action('admin_menu', [$manager, 'registerMenu']);
    }
}
