<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum Gender: string implements TranslatableInterface
{
    case Male = 'M';
    case Female = 'F';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::Male => $translator->trans('generic.gender_male', locale: $locale),
            self::Female => $translator->trans('generic.gender_female', locale: $locale),
        };
    }
}
