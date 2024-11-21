<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Honed\Table\Columns\Concerns\Formatters\FormatsBoolean;
use Honed\Table\Columns\Concerns\Formatters\FormatsMoney;
use Honed\Table\Columns\Concerns\Formatters\FormatsNumeric;
use Honed\Table\Columns\Concerns\Formatters\FormatsSeparator;

class Column extends BaseColumn
{
    use FormatsBoolean;
    use FormatsMoney;
    use FormatsNumeric;
    use FormatsSeparator;

    public function formatValue(mixed $value): mixed
    {
        if ($this->formatsBoolean()) {
            return $this->formatBoolean($value);
        }

        if ($this->formatsNumeric()) {
            return $this->formatNumeric($value);
        }

        if ($this->formatsMoney()) {
            return $this->formatMoney($value);
        }

        if ($this->formatsSeparator()) {
            return $this->formatSeparator($value);
        }

        return parent::formatValue($value);
    }
}
