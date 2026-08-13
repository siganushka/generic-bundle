<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\Tests\Fixtures\ApiController;
use Siganushka\GenericBundle\Tests\Fixtures\WebController;
use Symfony\Bundle\FrameworkBundle\Routing\AttributeRouteControllerLoader;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Routing\Route;

class CrudTest extends TestCase
{
    #[DataProvider('apiRoutesProvider')]
    public function testApiController(string $routeName, string $path, array $methods, string $controller, ?string $idRequirement): void
    {
        $loader = new AttributeRouteControllerLoader();
        $routes = $loader->load(ApiController::class);

        /** @var Route */
        $route = $routes->get($routeName);
        static::assertSame($path, $route->getPath());
        static::assertSame($methods, $route->getMethods());
        static::assertSame($controller, $route->getDefault('_controller'));
        static::assertSame($idRequirement, $route->getRequirement('id'));
    }

    #[DataProvider('webRoutesProvider')]
    public function testWebController(string $routeName, string $path, array $methods, string $controller, ?string $idRequirement): void
    {
        $loader = new AttributeRouteControllerLoader();
        $routes = $loader->load(WebController::class);

        /** @var Route */
        $route = $routes->get($routeName);
        static::assertSame($path, $route->getPath());
        static::assertSame($methods, $route->getMethods());
        static::assertSame($controller, $route->getDefault('_controller'));
        static::assertSame($idRequirement, $route->getRequirement('id'));
    }

    public static function apiRoutesProvider(): iterable
    {
        yield ['siganushka_generic_tests_fixtures_api_getcollection', '/api/users', ['GET'], 'Siganushka\GenericBundle\Tests\Fixtures\ApiController::getCollection', null];
        yield ['siganushka_generic_tests_fixtures_api_postcollection', '/api/users', ['POST'], 'Siganushka\GenericBundle\Tests\Fixtures\ApiController::postCollection', null];
        yield ['siganushka_generic_tests_fixtures_api_getitem', '/api/users/{id}', ['GET'], 'Siganushka\GenericBundle\Tests\Fixtures\ApiController::getItem', '\d+'];
        yield ['siganushka_generic_tests_fixtures_api_putitem', '/api/users/{id}', ['PUT', 'PATCH'], 'Siganushka\GenericBundle\Tests\Fixtures\ApiController::putItem', '\d+'];
        yield ['siganushka_generic_tests_fixtures_api_deleteitem', '/api/users/{id}', ['DELETE'], 'Siganushka\GenericBundle\Tests\Fixtures\ApiController::deleteItem', '\d+'];
    }

    public static function webRoutesProvider(): iterable
    {
        yield ['siganushka_generic_tests_fixtures_web_index', '/users', ['GET'], 'Siganushka\GenericBundle\Tests\Fixtures\WebController::index', Requirement::UUID_V7];
        yield ['siganushka_generic_tests_fixtures_web_new', '/users/new', ['GET', 'POST'], 'Siganushka\GenericBundle\Tests\Fixtures\WebController::new', Requirement::UUID_V7];
        yield ['siganushka_generic_tests_fixtures_web_show', '/users/{id}', ['GET'], 'Siganushka\GenericBundle\Tests\Fixtures\WebController::show', Requirement::UUID_V7];
        yield ['siganushka_generic_tests_fixtures_web_edit', '/users/{id}/edit', ['GET', 'POST'], 'Siganushka\GenericBundle\Tests\Fixtures\WebController::edit', Requirement::UUID_V7];
        yield ['siganushka_generic_tests_fixtures_web_delete', '/users/{id}/delete', ['GET'], 'Siganushka\GenericBundle\Tests\Fixtures\WebController::delete', Requirement::UUID_V7];
    }
}
