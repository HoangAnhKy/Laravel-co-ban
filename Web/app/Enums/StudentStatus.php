<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class StudentStatus extends Enum
{
    public const Di_hoc = 0;
    public const Bao_luu = 1;
    public const Bo_hoc = 2;

    public static function arrEnum(): array
    {
        return [
            "Đi học" => self::Di_hoc,
            "Bảo Lưu" => self::Bao_luu,
            "Bỏ học" => self::Bo_hoc,
        ];
    }

    public static function getkeyByValue($value): string
    {
        return array_search($value, self::arrEnum(), true);
    }
}
