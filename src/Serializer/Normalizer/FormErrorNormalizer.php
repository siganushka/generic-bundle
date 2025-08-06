<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Normalizer;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ProblemNormalizer;
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
        $type = $context[ProblemNormalizer::TYPE] ?? 'https://tools.ietf.org/html/rfc2616#section-10';
        $status = $context[ProblemNormalizer::STATUS] ?? Response::HTTP_UNPROCESSABLE_ENTITY;
        $detailAsString = 'Validation Failed';

        if ($this->translator) {
            $detailAsString = $this->translator->trans($detailAsString, domain: 'validators');
        }

        return [
            ProblemNormalizer::TYPE => $type,
            ProblemNormalizer::TITLE => Response::$statusTexts[$status],
            ProblemNormalizer::STATUS => $status,
            'detail' => $this->convertFormErrorsToArray($object) ?? $detailAsString,
            'errors' => $this->convertFormChildrenToArray($object),
        ];
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
