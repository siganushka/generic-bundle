<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Serializer\Normalizer;

use Siganushka\GenericBundle\Response\ProblemJsonResponse;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormErrorNormalizer implements NormalizerInterface
{
    public const TYPE = 'form_error_type';
    public const STATUS = 'form_error_status';
    public const ERRORS = 'form_error_errors';

    private array $defaultContext = [
        self::TYPE => 'https://symfony.com/errors/form',
        self::STATUS => ProblemJsonResponse::HTTP_UNPROCESSABLE_ENTITY,
        self::ERRORS => true,
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
        $type = $context[self::TYPE] ?? $this->defaultContext[self::TYPE];
        $status = $context[self::STATUS] ?? $this->defaultContext[self::STATUS];

        $detail = $this->convertFormErrorToStirng($object) ?? 'Validation Failed.';
        $data = ProblemJsonResponse::createAsArray($detail, $status, type: $type);

        if ($context[self::ERRORS] ?? $this->defaultContext[self::ERRORS]) {
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

    public function convertFormErrorToStirng(FormInterface $data, bool $showLabel = true): ?string
    {
        $errors = $data->getErrors(true);
        if (0 === $errors->count()) {
            return null;
        }

        $error = $errors->current();
        if (!$showLabel) {
            return $error->getMessage();
        }

        $form = $error->getOrigin();
        if (!$form || $form->isRoot()) {
            return $error->getMessage();
        }

        $label = $form->getConfig()->getOption('label');
        if (\is_string($label)) {
            $label = $this->translator?->trans($label, [], \is_string($domain = $form->getConfig()->getOption('translation_domain')) ? $domain : null) ?? $label;
        } elseif ($this->translator && $label instanceof TranslatableInterface) {
            $label = $label->trans($this->translator);
        } else {
            $label = $form->getName();
        }

        return \sprintf('%s: %s', $label, $error->getMessage());
    }

    private function convertFormChildrenToArray(FormInterface $data): array
    {
        $children = [];

        foreach ($data->all() as $child) {
            $childData = [
                'error' => $this->convertFormErrorToStirng($child, false),
            ];

            $isMultipleChoice = $child->getConfig()->hasOption('choices')
                && $child->getConfig()->getOption('multiple');

            if ($child->all() && !$isMultipleChoice) {
                $childData['children'] = $this->convertFormChildrenToArray($child);
            }

            $children[$child->getName()] = $childData;
        }

        return $children;
    }
}
