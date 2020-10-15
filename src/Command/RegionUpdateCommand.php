<?php

namespace Siganushka\GenericBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\GenericBundle\Model\Region;
use Siganushka\GenericBundle\Model\RegionInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->addOption('depth', null, InputOption::VALUE_OPTIONAL, '抓取深度，默认为 2 级，即省、直辖市、特别行政区和市、市辖区', 2)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $depth = (int) $input->getOption('depth');
        $array = [1, 2, 3, 4];

        if (!\in_array($depth, $array)) {
            throw new \RuntimeException(sprintf('抓取深度只能为 %s！级', implode(', ', $array)));
        }

        $this->syncFromRemote($output, $depth);

        return Command::SUCCESS;
    }

    protected function syncFromRemote(OutputInterface $output, int $depth, ?RegionInterface $parent = null)
    {
        $query = $this->entityManager->createQueryBuilder()
            ->select('r.code')
            ->from(Region::class, 'r')
            ->getQuery();

        $result = $query->getResult();
        $codes = array_column($result, 'code');

        foreach ($this->doRequest($parent) as $data) {
            $messages = sprintf('[%d] %s', $data['id'], $data['fullname']);

            if (\in_array($data['id'], $codes)) {
                $output->writeln("<comment>{$messages} 存在，已跳过！</comment>");
                continue;
            }

            if ($parent && ($parent->getDepth() + 1) >= $depth) {
                continue;
            }

            $region = new Region();
            $region->setParent($parent);
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
            usleep(200000); // rate limit

            try {
                $this->syncFromRemote($output, $depth, $region);
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
            $query['id'] = $parent->getCode();
        }

        $response = $this->httpClient->request('GET', 'https://apis.map.qq.com/ws/district/v1/getchildren', ['query' => $query]);

        $data = $response->toArray();
        if (0 != $data['status']) {
            throw new \RuntimeException($data['message'], $data['status']);
        }

        return $data['result'][0] ?? [];
    }
}
