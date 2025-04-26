<?php
/**
 * Plugin Name:     Book Management
 * Plugin URI:      https://www.veronalabs.com
 * Plugin Prefix:   BM
 * Description:     Example WordPress Plugin Based on Rabbit Framework to manage books!
 * Author:          Amir Pirmoradian
 * Author URI:      https://veronalabs.com
 * Text Domain:     book-managment
 * Domain Path:     /languages
 * Version:         1.0
 */

use Piramir\BookManagement\ServiceProviders\MigrationServiceProvider;
use Piramir\BookManagement\ServiceProviders\PostTypeServiceProvider;
use Rabbit\Application;
use Rabbit\Redirects\RedirectServiceProvider;
use Rabbit\Database\DatabaseServiceProvider;
use Rabbit\Logger\LoggerServiceProvider;
use Rabbit\Plugin;
use Rabbit\Redirects\AdminNotice;
use Rabbit\Templates\TemplatesServiceProvider;
use Rabbit\Utils\Singleton;
use League\Container\Container;

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require dirname(__FILE__) . '/vendor/autoload.php';
}

/**
 * Class BookManagementPluginInit
 * @package BookManagementPluginInit
 */
class BookManagementPluginInit extends Singleton
{
    /**
     * @var Container
     */
    private $application;

    /**
     * BookManagementPluginInit constructor.
     */
    public function __construct()
    {
        $this->application = Application::get()->loadPlugin(__DIR__, __FILE__, 'config');
        $this->init();
    }

    public function init()
    {
        try {

            /**
             * Load service providers
             */
            $this->application->addServiceProvider(RedirectServiceProvider::class);
            $this->application->addServiceProvider(DatabaseServiceProvider::class);
            $this->application->addServiceProvider(TemplatesServiceProvider::class);
            $this->application->addServiceProvider(LoggerServiceProvider::class);
            // Load your own service providers here...

            $this->application->addServiceProvider(MigrationServiceProvider::class);

            /**
             * Activation hooks
             */
            $this->application->onActivation(function () {
                $repository = $this->application->get('book.repository');
                $repository->migrate();
            });

            /**
             * Deactivation hooks
             */
            $this->application->onDeactivation(function () {
                $repository = $this->application->get('book.repository');
                $repository->uninstall();
            });

            $this->application->boot(function (Plugin $plugin) {
                $plugin->loadPluginTextDomain();

                // load template
                $this->application->template('plugin-template.php', ['foo' => 'bar']);

                ///...

            });

        } catch (Exception $e) {
            /**
             * Print the exception message to admin notice area
             */
            add_action('admin_notices', function () use ($e) {
                AdminNotice::permanent(['type' => 'error', 'message' => $e->getMessage()]);
            });

            /**
             * Log the exception to file
             */
            add_action('init', function () use ($e) {
                if ($this->application->has('logger')) {
                    $this->application->get('logger')->warning($e->getMessage());
                }
            });
        }
    }

    /**
     * @return Container
     */
    public function getApplication()
    {
        return $this->application;
    }
}

/**
 * Returns the main instance of BookManagementPluginInit.
 *
 * @return BookManagementPluginInit
 */
function examplePlugin()
{
    return BookManagementPluginInit::get();
}

examplePlugin();