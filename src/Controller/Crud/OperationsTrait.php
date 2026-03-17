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
     * @param class-string<object>            $entityName
     * @param class-string<FormTypeInterface> $entityForm
     */
    protected function configureCrud(
        string $entityName,
        ?string $entityIdentifier = null,
        ?string $entityForm = null,
        ?string $queryDtoClass = null,
        ?array $serializationCollectionContext = null,
        ?array $serializationItemContext = null,
        ?bool $transactionUsed = null,
        ?bool $paginationUsed = null,
    ): void {
        $this->entityName = $entityName;
        $this->entityIdentifier = $entityIdentifier ?? 'id';
        $this->entityForm = $entityForm ?? FormType::class;
        $this->queryDtoClass = $queryDtoClass;
        $this->serializationCollectionContext = $serializationCollectionContext ?? [AbstractNormalizer::GROUPS => \sprintf('%s:collection', ClassUtils::generateAlias($this->entityName))];
        $this->serializationItemContext = $serializationItemContext ?? [AbstractNormalizer::GROUPS => \sprintf('%s:item', ClassUtils::generateAlias($this->entityName))];
        $this->transactionUsed = $transactionUsed ?? false;
        $this->paginationUsed = $paginationUsed ?? true;
    }

    protected function createEntityQueryBuilder(string $alias, ?object $dto): QueryBuilder
    {
        $er = $this->entityManager->getRepository($this->entityName);

        return $er instanceof GenericEntityRepository
            ? $er->createQueryBuilderWithOrderBy($alias)
            : $er->createQueryBuilder($alias);
    }

    protected function createEntity(mixed ...$args): object
    {
        $er = $this->entityManager->getRepository($this->entityName);

        return $er instanceof GenericEntityRepository
            ? $er->createNew(...$args)
            : (new \ReflectionClass($er->getClassName()))->newInstanceArgs($args);
    }

    protected function findEntity(string $_id): object
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
            \call_user_func($func);
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
