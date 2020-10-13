<?php

namespace Siganushka\GenericBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Model\Region;
use Siganushka\GenericBundle\Model\RegionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RegionsUpdateCommand extends Command
{
    const MAX_DEPTH = 1;
    const DIRECT_CODES = [
        110000 => 110100,
        120000 => 120100,
        310000 => 310100,
        500000 => 500100,
        810000 => 810100,
        820000 => 820100,
    ];

    protected static $defaultName = 'siganushka:regions:update';

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
            ->addArgument('key', InputArgument::REQUIRED, '腾讯地图开发密钥')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $key = $input->getArgument('key');
        if (empty($key)) {
            throw new \RuntimeException('腾讯地图开发密钥 key 不能为！');
        }

        $this->syncFromRemote($output, $key);

        return Command::SUCCESS;
    }

    protected function syncFromRemote(OutputInterface $output, string $key, ?RegionInterface $parent = null)
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('r.code')
            ->from(Region::class, 'r')
            ->getQuery();

        $result = $query->getResult();
        $codes = array_column($result, 'code');

        foreach ($this->doRequest($key, $parent) as $data) {
            $messages = sprintf('[%d] %s', $data['id'], $data['fullname']);

            if (\in_array($data['id'], $codes)) {
                $output->writeln("<comment>{$messages} 存在，已跳过！</comment>");
                continue;
            }

            // 直辖市把 2 级变为 3 级
            $newParent = $this->getNewParent($parent);
            if ($newParent && $newParent->getDepth() >= self::MAX_DEPTH) {
                continue;
            }

            $region = new Region();
            $region->setParent($newParent);
            $region->setCode($data['id']);
            $region->setName($data['fullname']);
            $region->setLatitude($data['location']['lat']);
            $region->setLongitude($data['location']['lng']);
            $region->recalculateDepth();

            if (!empty($data['pinyin'])) {
                $region->setPinyin(implode('', $data['pinyin']));
            }

            $this->entityManager->persist($region);
            $this->entityManager->flush();

            $output->writeln("<info>{$messages} 添加成功！</info>");
            usleep(200000);

            try {
                $this->syncFromRemote($output, $key, $region);
            } catch (\Throwable $th) {
                if (363 !== $th->getCode()) {
                    throw $th;
                }

                $output->writeln("<comment>{$messages} 没有子节点，跳过！</comment>");
                continue;
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @see https://lbs.qq.com/service/webService/webServiceGuide/webServiceDistrict
     * @see DZGBZ-6BQRQ-NSQ5Z-GQI3W-Z3G2K-OMB56
     */
    protected function doRequest(string $key, ?RegionInterface $parent)
    {
        $query = ['key' => $key];
        if ($parent instanceof RegionInterface) {
            $query['id'] = $parent->getCode();
        }

        $response = $this->httpClient->request('GET', 'https://apis.map.qq.com/ws/district/v1/getchildren', ['query' => $query]);

        $data = $response->toArray();
        if (0 != $data['status']) {
            throw new \RuntimeException($data['message'], $data['status']);
        }

        return current($data['result']);
    }

    protected function getNewParent(?RegionInterface $parent)
    {
        if (null === $parent || !isset(self::DIRECT_CODES[$parent->getCode()])) {
            return $parent;
        }

        $code = self::DIRECT_CODES[$parent->getCode()];
        $query = $this->entityManager->createQueryBuilder()
            ->select('r')
            ->from(Region::class, 'r')
            ->where('r.code = :code')
            ->setParameter('code', $code)
            ->getQuery();

        if ($entity = $query->getOneOrNullResult()) {
            return $entity;
        }

        $newParent = new Region();
        $newParent->setParent($parent);
        $newParent->setCode($code);
        $newParent->setName('直辖市、行政区');
        $newParent->setLatitude($parent->getLatitude());
        $newParent->setLongitude($parent->getLongitude());
        $newParent->recalculateDepth();

        $this->entityManager->persist($newParent);
        $this->entityManager->flush();

        return $newParent;
    }
}
