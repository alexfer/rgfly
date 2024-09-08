<?php declare(strict_types=1);

namespace App\Entity\MarketPlace\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum EnumStoreOptions: int implements TranslatableInterface
{
    case On = 1;
    case Off = 0;

    /**
     * @param TranslatorInterface $translator
     * @param string|null $locale
     * @return string
     */
    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::On => $translator->trans('text.schedule.backup.on', locale: $locale),
            self::Off => $translator->trans('text.schedule.backup.off', locale: $locale),
        };
    }
}
