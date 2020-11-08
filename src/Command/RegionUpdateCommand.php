<?php

namespace Siganushka\GenericBundle\Command;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Siganushka\GenericBundle\Entity\Region;
use Siganushka\GenericBundle\Entity\RegionInterface;
use Siganushka\GenericBundle\SiganushkaGenericBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegionUpdateCommand extends Command
{
    protected static $defaultName = 'siganushka:region:update';

    private $objectManager;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->objectManager = $managerRegistry->getManagerForClass(Region::class);

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('更新行政区划数据（来原 Github）');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $reflection = new \ReflectionClass(SiganushkaGenericBundle::class);

        $json = \dirname($reflection->getFileName()).'/Resources/data/pca-code.json';
        if (!file_exists($json)) {
            throw new \InvalidArgumentException(sprintf('File "%s" is not found', $json));
        }

        $json = file_get_contents($json);
        $data = json_decode($json, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \UnexpectedValueException(json_last_error_msg());
        }

        // Manually assign id
        $metadata = $this->objectManager->getClassMetadata(Region::class);
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $this->import($output, $data);
        $this->objectManager->flush();

        // Compatible symfony <=5.1
        if (\defined('Symfony\Component\Console\Command\Command::SUCCESS')) {
            return Command::SUCCESS;
        } else {
            return 0;
        }
    }

    protected function import(OutputInterface $output, array $data, ?RegionInterface $parent = null)
    {
        foreach ($data as $value) {
            $region = new Region();
            $region->setParent($parent);
            $region->setCode($value['code']);
            $region->setName($value['name']);

            $messages = sprintf('[%d] %s', $region->getCode(), $region->getName());

            $newParent = $this->objectManager->find(Region::class, $region->getCode());
            if ($newParent) {
                $output->writeln("<comment>{$messages} 存在，已跳过！</comment>");
            } else {
                $output->writeln("<info>{$messages} 添加成功！</info>");
                $this->objectManager->persist($region);
            }

            if (isset($value['children'])) {
                $this->import($output, $value['children'], $newParent ?: $region);
            }
        }
    }
}
