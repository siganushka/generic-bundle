<?php

namespace Siganushka\GenericBundle\Command;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Model\Region;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RegionUpdateCommand extends Command
{
    const URL = 'https://raw.githubusercontent.com/modood/Administrative-divisions-of-China/master/dist/pca-code.json';

    protected static $defaultName = 'siganushka:region:update';

    private $httpClient;
    private $connection;
    private $metadata;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager)
    {
        $this->httpClient = $httpClient;
        $this->connection = $entityManager->getConnection();
        $this->metadata = $entityManager->getClassMetadata(Region::class);

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('更新行政区划数据（来原 Github）');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->httpClient->request('GET', self::URL);
        $contents = $response->getContent();

        $regions = json_decode($contents, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \UnexpectedValueException(json_last_error_msg());
        }

        $this->import($output, $regions);

        return Command::SUCCESS;
    }

    protected function import(OutputInterface $output, array $regions, ?int $parentId = null)
    {
        foreach ($regions as $region) {
            $code = str_pad($region['code'], 6, 0);
            $name = mb_substr($region['name'], 0, 32);

            $messages = sprintf('[%d] %s', $code, $name);

            $queryBuilder = $this->connection->createQueryBuilder()
                ->insert($this->metadata->getTableName())
                ->values(['id' => '?', 'parent_id' => '?', 'name' => '?']);

            try {
                $this->connection->executeStatement($queryBuilder->getSQL(), [$code, $parentId, $name]);
            } catch (UniqueConstraintViolationException $th) {
                $output->writeln("<comment>{$messages} 存在，已跳过！</comment>");
                continue;
            }

            $output->writeln("<info>{$messages} 添加成功！</info>");

            if (!empty($region['children'])) {
                $this->import($output, $region['children'], $code);
            }
        }
    }
}
