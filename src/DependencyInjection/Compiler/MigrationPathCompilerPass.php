<?php
namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MigrationPathCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('doctrine.default_connection')) {
            throw new \LogicException('The doctrine.default_connection parameter is not defined.');
        }

        $defaultConnection = $container->getParameter('doctrine.default_connection');
        $connectionConfig = $container->getDefinition('doctrine.dbal.' . $defaultConnection . '_connection')->getArgument(0);

        if(strpos($container->resolveEnvPlaceholders($connectionConfig['url'], true), 'sqlite') !== false) {
            $dsn = array('scheme' => 'sqlite');
        } else {
            $dsn = parse_url($container->resolveEnvPlaceholders($connectionConfig['url'], true));
        }
        // Determine the appropriate migrations path
        if ($dsn['scheme'] == 'mysql') {
            $migrationPath = '%kernel.project_dir%/migrations/mysql';
        } elseif ($dsn['scheme'] == 'postgresql') {
            $migrationPath = '%kernel.project_dir%/migrations/postgresql';
        } elseif ($dsn['scheme'] == 'sqlite') {
            $migrationPath = '%kernel.project_dir%/migrations/sqlite';
        } else {
            throw new \InvalidArgumentException('Unsupported database type.');
        }
        
        // Find the doctrine.migrations.configuration service definition
        $configurationDefinition = $container->findDefinition('doctrine.migrations.configuration');

        // Set the migration paths dynamically
        $configurationDefinition->addMethodCall('addMigrationsDirectory', [
            'DoctrineMigrations',
            $migrationPath
        ]);
    }
}
