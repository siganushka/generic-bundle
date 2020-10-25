<?php

namespace Siganushka\GenericBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Siganushka\GenericBundle\Model\Region;
use Siganushka\GenericBundle\Model\RegionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RegionUpdateCommand extends Command
{
    const URL = 'https://raw.githubusercontent.com/modood/Administrative-divisions-of-China/master/dist/pca-code.json';

    protected static $defaultName = 'siganushka:region:update';

    private $httpClient;
    private $entityManager;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager)
    {
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('更新行政区划数据（来原 Github）');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // manually assign id
        $metadata = $this->entityManager->getClassMetadata(Region::class);
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);

        $response = $this->httpClient->request('GET', self::URL);
        $contents = $response->getContent();

        $data = json_decode($contents, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \UnexpectedValueException(json_last_error_msg());
        }

        // $data = [
        //     [
        //         'code' => 1,
        //         'name' => 'foo1',
        //         'children' => [
        //             [
        //                 'code' => 11,
        //                 'name' => 'foo11',
        //                 'children' => [
        //                     [
        //                         'code' => 1111,
        //                         'name' => 'foo1111'
        //                     ]
        //                 ]
        //             ]
        //         ],
        //     ],
        // ];

        $this->import($output, $data);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    protected function import(OutputInterface $output, array $data, ?RegionInterface $parent = null)
    {
        foreach ($data as $value) {
            $region = new Region();
            $region->setParent($parent);
            $region->setCode($value['code']);
            $region->setName($value['name']);

            $messages = sprintf('[%d] %s', $region->getCode(), $region->getName());

            $newParent = $this->entityManager->find(Region::class, $region->getCode());
            if ($newParent) {
                $output->writeln("<comment>{$messages} 存在，已跳过！</comment>");
            } else {
                $output->writeln("<info>{$messages} 添加成功！</info>");
                $this->entityManager->persist($region);
            }

            if (isset($value['children'])) {
                $this->import($output, $value['children'], $newParent ?: $region);
            }
        }
    }
}
