<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Normalizer;

use Siganushka\GenericBundle\Response\ProblemJsonResponse;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FormErrorNormalizer implements NormalizerInterface
{
    public const TYPE = 'form_error_type';
    public const STATUS = 'form_error_status';
    public const WITH_ERRORS = 'form_error_with_errors';

    private array $defaultContext = [
        self::TYPE => 'https://symfony.com/errors/form',
        self::STATUS => ProblemJsonResponse::HTTP_UNPROCESSABLE_ENTITY,
        self::WITH_ERRORS => true,
    ];

    public function __construct(array $defaultContext = [])
    {
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
    }

    /**
     * @param FormInterface $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $type = $context[self::TYPE] ?? $this->defaultContext[self::TYPE];
        $status = $context[self::STATUS] ?? $this->defaultContext[self::STATUS];

        $detail = $this->convertFormFirstError($object);
        $data = ProblemJsonResponse::createAsArray($detail, $status, type: $type);

        if ($context[self::WITH_ERRORS] ?? $this->defaultContext[self::WITH_ERRORS]) {
            $data['errors'] = $this->convertFormChildrenToArray($object);
        }

        return $data;
    }

    public function supportsNormalization($data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof FormInterface && $data->isSubmitted() && !$data->isValid();
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            FormInterface::class => false,
        ];
    }

    private function convertFormFirstError(FormInterface $data): string
    {
        $error = $data->getErrors(true)->current();

        $form = $error->getOrigin();
        if (!$form || $form->isRoot()) {
            return $error->getMessage();
        }

        return \sprintf('[%s] %s', $form->getName(), $error->getMessage());
    }

    private function convertFormErrorsToArray(FormInterface $data): ?string
    {
        foreach ($data->getErrors() as $error) {
            return $error->getMessage();
        }

        return null;
    }

    private function convertFormChildrenToArray(FormInterface $data): array
    {
        $children = [];

        foreach ($data->all() as $child) {
            $childData = [
                'error' => $this->convertFormErrorsToArray($child),
            ];

            if ($child->all()) {
                $childData['children'] = $this->convertFormChildrenToArray($child);
            }

            $children[$child->getName()] = $childData;
        }

        return $children;
    }
}
