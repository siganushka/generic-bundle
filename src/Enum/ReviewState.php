<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum ReviewState: string implements TranslatableInterface
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans('generic.review_state.'.$this->value, locale: $locale);
    }

    public function theme(): string
    {
        return match ($this) {
            self::Draft => 'secondary',
            self::Pending => 'primary',
            self::Approved => 'success',
            self::Rejected => 'danger',
        };
    }
}
