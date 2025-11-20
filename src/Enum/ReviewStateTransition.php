<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum ReviewStateTransition: string implements TranslatableInterface
{
    case SubmitReview = 'submit_review';
    case CancelReview = 'cancel_review';
    case Approve = 'approve';
    case Reject = 'reject';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans('generic.review_state_transition.'.$this->value, locale: $locale);
    }

    public function theme(): string
    {
        return match ($this) {
            self::SubmitReview => 'primary',
            self::CancelReview => 'primary',
            self::Approve => 'success',
            self::Reject => 'danger',
        };
    }
}
