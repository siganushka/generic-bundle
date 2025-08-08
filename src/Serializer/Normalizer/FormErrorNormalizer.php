<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Normalizer;

use Siganushka\GenericBundle\Response\ProblemResponse;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormErrorNormalizer implements NormalizerInterface
{
    public function __construct(private readonly ?TranslatorInterface $translator = null)
    {
    }

    /**
     * @param FormInterface $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $detailClosure = fn (string $detail): string => $this->translator?->trans($detail, domain: 'validators') ?? $detail;

        $detail = $this->convertFormErrorsToArray($object) ?? $detailClosure('Validation Failed');

        $data = ProblemResponse::createAsArray($detail, Response::HTTP_UNPROCESSABLE_ENTITY, type: 'https://symfony.com/errors/form');
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
