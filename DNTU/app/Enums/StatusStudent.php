<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class StatusStudent extends Enum
{
    public const Dang_hoc = 1;
    public const Bo_hoc = 2;
    public const Bao_luu = 3;

    public static function arrStatus(): array
    {
        return [
            'Đang học' => self::Dang_hoc,
            'Bỏ học' => self::Bo_hoc,
            'Bảo lưu' => self::Bao_luu,
        ];
    }

    public static function getKeyInValues($obj): string
    {
        return array_search($obj, self::arrStatus(), true);
    }
}
