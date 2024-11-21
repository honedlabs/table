<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Honed\Table\Columns\Concerns\Formatters\FormatsMoney;
use Honed\Table\Columns\Concerns\Formatters\FormatsNumeric;

class NumericColumn extends FallbackColumn
{
    use FormatsMoney;
    use FormatsNumeric;

    public function setUp(): void
    {
        $this->setType('numeric');
    }

    public function defaultFallback(): mixed
    {
        return config('table.fallback.numeric', parent::defaultFallback());
    }

    public function formatValue(mixed $value): mixed
    {
        if ($this->formatsNumeric()) {
            return $this->formatNumeric($value);
        }

        if ($this->formatsMoney()) {
            return $this->formatMoney($value);
        }

        return parent::formatValue($value);
    }
}
