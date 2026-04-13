<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Controller\Crud;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Siganushka\GenericBundle\Repository\GenericEntityRepository;
use Siganushka\GenericBundle\Utils\ClassUtils;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Contracts\Service\Attribute\Required;

trait OperationsTrait
{
    public const OPERATION_READ = 'READ';
    public const OPERATION_CREATE = 'CREATE';
    public const OPERATION_UPDATE = 'UPDATE';
    public const OPERATION_DELETE = 'DELETE';

    #[Required]
    public EntityManagerInterface $entityManager;

    #[Required]
    public FormFactoryInterface $formFactory;

    /**
     * @var class-string<object>
     */
    protected string $entityName;

    protected string $entityIdentifier;

    /**
     * @var class-string<FormTypeInterface>
     */
    protected string $entityForm;

    protected ?string $queryDtoClass;
    protected array $serializationCollectionContext;
    protected array $serializationItemContext;
    protected bool $transactionUsed;
    protected bool $paginationUsed;

    /**
     * @param class-string<object>                 $entityName
     * @param class-string<FormTypeInterface>|null $entityForm
     * @param class-string<object>|null            $queryDtoClass
     * @param array<int, string>|null              $serializationCollectionGroups
     * @param array<int, string>|null              $serializationItemGroups
     * @param array<string, mixed>|null            $serializationCollectionContext
     * @param array<string, mixed>|null            $serializationItemContext
     */
    protected function configureCrud(
        string $entityName,
        ?string $entityIdentifier = null,
        ?string $entityForm = null,
        ?string $queryDtoClass = null,
        ?array $serializationCollectionGroups = null,
        ?array $serializationItemGroups = null,
        ?array $serializationCollectionContext = null,
        ?array $serializationItemContext = null,
        ?bool $transactionUsed = null,
        ?bool $paginationUsed = null,
    ): void {
        $entityAlias = ClassUtils::generateAlias($entityName);
        $serializationCollectionGroups ??= \sprintf('%s:collection', $entityAlias);
        $serializationItemGroups ??= \sprintf('%s:collection', $entityAlias);

        $this->entityName = $entityName;
        $this->entityIdentifier = $entityIdentifier ?? 'id';
        $this->entityForm = $entityForm ?? FormType::class;
        $this->queryDtoClass = $queryDtoClass;
        $this->serializationCollectionContext = $serializationCollectionContext ?? [AbstractNormalizer::GROUPS => $serializationCollectionGroups];
        $this->serializationItemContext = $serializationItemContext ?? [AbstractNormalizer::GROUPS => $serializationItemGroups];
        $this->transactionUsed = $transactionUsed ?? false;
        $this->paginationUsed = $paginationUsed ?? true;
    }

    protected function createEntityQueryBuilder(string $alias): QueryBuilder
    {
        $er = $this->entityManager->getRepository($this->entityName);

        return $er instanceof GenericEntityRepository
            ? $er->createQueryBuilderWithOrderBy($alias)
            : $er->createQueryBuilder($alias);
    }

    protected function createEntity(): object
    {
        $er = $this->entityManager->getRepository($this->entityName);

        return $er instanceof GenericEntityRepository
            ? $er->createNew()
            : (new \ReflectionClass($er->getClassName()))->newInstanceArgs();
    }

    protected function findEntity(int|string $_id): object
    {
        $er = $this->entityManager->getRepository($this->entityName);

        return $er->findOneBy([$this->entityIdentifier => $_id])
            ?? throw new NotFoundHttpException('Not Found');
    }

    protected function createEntityForm(object $data, array $options = []): FormInterface
    {
        return $this->formFactory->create($this->entityForm, $data, $options);
    }

    protected function runInTransaction(callable $func): void
    {
        if ($this->transactionUsed) {
            $this->entityManager->wrapInTransaction($func);
        } else {
            \call_user_func($func, $this->entityManager);
        }
    }

    protected function isGrantedForOperation(string $operation, object $entity): bool
    {
        return \in_array($operation, [
            self::OPERATION_READ,
            self::OPERATION_CREATE,
            self::OPERATION_UPDATE,
            self::OPERATION_DELETE,
        ]);
    }
}
