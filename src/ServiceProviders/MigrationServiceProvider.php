<?php

namespace Piramir\BookManagement\ServiceProviders;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\ServiceProviderInterface;
use Piramir\BookManagement\Repositories\BookRepository;

class MigrationServiceProvider extends AbstractServiceProvider implements ServiceProviderInterface
{
    /**
     * Services provided by this provider.
     *
     * @var array
     */
    protected $provides = [
        'book.repository',
    ];

    /**
     * Register services into the container.
     *
     * @return void
     */
    public function register()
    {
        $container = $this->getContainer();

        $container->add('book.repository', function () {
            return new BookRepository();
        });
    }

}
