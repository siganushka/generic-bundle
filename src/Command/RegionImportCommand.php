<?php

namespace Siganushka\GenericBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Model\Region;
use Siganushka\GenericBundle\SiganushkaGenericBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class RegionImportCommand extends Command
{
    protected static $defaultName = 'siganushka:region:import';

    protected $kernel;
    protected $entityManager;

    public function __construct(KernelInterface $kernel, EntityManagerInterface $entityManager)
    {
        $this->kernel = $kernel;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('导入行政区划数据（来原腾讯地图）')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ref = new \ReflectionClass(SiganushkaGenericBundle::class);
        $sqlFile = \dirname($ref->getFileName()).'/Resources/files/region.sql';

        if (!file_exists($sqlFile)) {
            throw new \InvalidArgumentException(sprintf('Region sql file "%s" is not found', $sqlFile));
        }

        $connection = $this->entityManager->getConnection();
        $metadata = $this->entityManager->getClassMetadata(Region::class);

        $sql = file_get_contents($sqlFile);
        $sql = str_replace('__TABLE__', $metadata->getTableName(), $sql);

        $statement = $connection->prepare($sql);
        $statement->execute();

        $output->writeln('<info>导入行政区划数据成功！</info>');

        return Command::SUCCESS;
    }
}
