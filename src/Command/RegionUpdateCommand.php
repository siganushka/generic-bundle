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
        foreach ($this->doRequest($parent) as $data) {
            $messages = sprintf('[%d] %s', $data['id'], $data['fullname']);

            if ($depth > 3) {
                $output->writeln("<comment>{$messages} 层级过深，已跳过！</comment>");
                continue;
            }

            try {
                $region = $this->persist($parent, $data);
            } catch (UniqueConstraintViolationException $th) {
                $output->writeln("<comment>{$messages} 存在，已跳过！</comment>");
                continue;
            }

            $output->writeln("<info>{$messages} 添加成功！</info>");
            usleep(200000); // rate limit

            try {
                $this->syncFromRemote($output, $region, $depth);
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

    public function persist(?RegionInterface $parent, array $data)
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
            $data['id'],
            $parent instanceof RegionInterface ? $parent->getId() : null,
            $data['fullname'],
            $data['location']['lat'],
            $data['location']['lng'],
            empty($data['pinyin']) ? null : implode('', $data['pinyin']),
        ]);

        return $this->entityManager->find(Region::class, $connection->lastInsertId());
    }
}
