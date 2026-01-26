<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tests;

use PHPUnit\Framework\TestCase;
use Siganushka\GenericBundle\DependencyInjection\Compiler\DoctrineResolveTargetEntityPass;
use Siganushka\GenericBundle\SiganushkaGenericBundle;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SiganushkaGenericBundleTest extends TestCase
{
    public function testAll(): void
    {
        $container = new ContainerBuilder();

        $bundle = new SiganushkaGenericBundle();
        $bundle->build($container);

        $passes = $container->getCompilerPassConfig()->getBeforeOptimizationPasses();
        $classNameOfPasses = array_map(static fn (CompilerPassInterface $compiler) => $compiler::class, $passes);

        static::assertContains(DoctrineResolveTargetEntityPass::class, $classNameOfPasses);
    }
}
