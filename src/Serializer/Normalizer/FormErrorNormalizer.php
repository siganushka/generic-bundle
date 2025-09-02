<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Normalizer;

use Siganushka\GenericBundle\Response\ProblemJsonResponse;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormErrorNormalizer implements NormalizerInterface
{
    public const TYPE = 'form_error_type';
    public const STATUS = 'form_error_status';

    private array $defaultContext = [
        self::TYPE => 'https://symfony.com/errors/form',
        self::STATUS => ProblemJsonResponse::HTTP_UNPROCESSABLE_ENTITY,
    ];

    public function __construct(array $defaultContext = [], private readonly ?TranslatorInterface $translator = null)
    {
        $this->defaultContext = array_merge($this->defaultContext, $defaultContext);
    }

    /**
     * @param FormInterface $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $detailClosure = fn (string $detail): string => $this->translator?->trans($detail, domain: 'validators') ?? $detail;

        $type = $context[self::TYPE] ?? $this->defaultContext[self::TYPE];
        $status = $context[self::STATUS] ?? $this->defaultContext[self::STATUS];
        $detail = $this->convertFormErrorsToArray($object) ?? $detailClosure('Validation Failed');

        $data = ProblemJsonResponse::createAsArray($detail, $status, type: $type);
        $data['errors'] = $this->convertFormChildrenToArray($object);

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
