<?php

namespace EleganceCMS\DataSynchronize\Enums;

use EleganceCMS\Base\Supports\Enum;

class ExportColumnType extends Enum
{
    public const DROPDOWN = 'dropdown';

    public const TEXT = 'text';

    public const NUMBER = 'number';

    public const DATETIME = 'datetime';

    public const BOOLEAN = 'boolean';
}
