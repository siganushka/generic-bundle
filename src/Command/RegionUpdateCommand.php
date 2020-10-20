<?php

namespace Siganushka\GenericBundle\Command;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Model\Region;
use Siganushka\GenericBundle\Model\RegionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RegionUpdateCommand extends Command
{
    const KEY = 'DZGBZ-6BQRQ-NSQ5Z-GQI3W-Z3G2K-OMB56';

    const DIRECT_CODES = [
        110000 => 110100,
        120000 => 120100,
        310000 => 310100,
        500000 => 500100,
        810000 => 810100,
        820000 => 820100,
    ];

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
        $this
            ->setDescription('更新行政区划数据（来原腾讯地图）')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->syncFromRemote($output);

        return Command::SUCCESS;
    }

    protected function syncFromRemote(OutputInterface $output, ?RegionInterface $parent = null, int $depth = 0)
    {
        ++$depth;

        if ($parent && isset(self::DIRECT_CODES[$parent->getId()])) {
            ++$depth;
        }

        if ($depth > 2) {
            return;
        }

        foreach ($this->doRequest($parent) as $data) {
            $messages = sprintf('[%d] %s', $data['id'], $data['fullname']);

            try {
                $entity = $this->generateEntity($data, $parent);
            } catch (UniqueConstraintViolationException $th) {
                $output->writeln("<comment>{$messages} 存在，已跳过！</comment>");
                continue;
            }

            $output->writeln("<info>{$messages} 添加成功！</info>");
            usleep(200000); // rate limit

            try {
                $this->syncFromRemote($output, $entity, $depth);
            } catch (\Throwable $th) {
                if (363 !== $th->getCode()) {
                    throw $th;
                }

                $output->writeln("<comment>{$messages} 没有子节点，跳过！</comment>");
                continue;
            }
        }
    }

    /**
     * @see https://lbs.qq.com/service/webService/webServiceGuide/webServiceDistrict
     */
    protected function doRequest(?RegionInterface $parent): array
    {
        $query = ['key' => self::KEY];
        if ($parent instanceof RegionInterface) {
            $query['id'] = $parent->getId();
        }

        $response = $this->httpClient->request('GET', 'https://apis.map.qq.com/ws/district/v1/getchildren', ['query' => $query]);

        $data = $response->toArray();
        if (0 != $data['status']) {
            throw new \RuntimeException($data['message'], $data['status']);
        }

        return $data['result'][0] ?? [];
    }

    public function generateEntity(array $rawData, ?RegionInterface $parent)
    {
        $connection = $this->entityManager->getConnection();
        $metadata = $this->entityManager->getClassMetadata(Region::class);

        $queryBuilder = $connection->createQueryBuilder()
            ->insert($metadata->getTableName())
            ->values([
                'id' => '?',
                'parent_id' => '?',
                'name' => '?',
                'latitude' => '?',
                'longitude' => '?',
                'pinyin' => '?',
            ]);

        $connection->executeStatement($queryBuilder->getSQL(), [
            $rawData['id'],
            $parent instanceof RegionInterface ? $parent->getId() : null,
            $rawData['fullname'],
            $rawData['location']['lat'],
            $rawData['location']['lng'],
            empty($rawData['pinyin']) ? null : implode('', $rawData['pinyin']),
        ]);

        return $this->entityManager->find(Region::class, $connection->lastInsertId());
    }
}
