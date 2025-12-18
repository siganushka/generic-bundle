<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RoutesTest extends TestCase
{
    protected RouteCollection $routes;

    protected function setUp(): void
    {
        $loader = new PhpFileLoader(new FileLocator(__DIR__.'/../config/'), 'dev');
        $this->routes = $loader->load('routes.php');
    }

    public function testAll(): void
    {
        $routeNames = [];
        foreach (self::routesProvider() as $route) {
            $routeNames[] = $route[0];
        }

        static::assertSame($routeNames, array_keys($this->routes->all()));
    }

    #[DataProvider('routesProvider')]
    public function testRotues(string $routeName, string $path, array $methods, string $controller): void
    {
        /** @var Route */
        $route = $this->routes->get($routeName);

        static::assertSame($path, $route->getPath());
        static::assertSame($methods, $route->getMethods());
        static::assertSame($controller, $route->getDefault('_controller'));
    }

    public static function routesProvider(): iterable
    {
        yield ['siganushka_generic_form', '/_form', [], 'siganushka_generic.form.controller'];
    }
}
