<?php

namespace Piramir\BookManagement\ServiceProviders;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Rabbit\Contracts\BootablePluginProviderInterface;
use Piramir\BookManagement\Managers\BookPostTypeManager;

class PostTypeServiceProvider extends AbstractServiceProvider implements BootablePluginProviderInterface
{
    protected $provides = [
        'book.post-type',
    ];

    public function register(): void
    {
        $this->getContainer()->add('book.post-type', function () {
            return new BookPostTypeManager();
        });
    }

    public function bootPlugin(): void
    {
        $manager = $this->getContainer()->get('book.post-type');
        $manager->register();
    }
}
