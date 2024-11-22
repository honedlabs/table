<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns\Formatters;

use Closure;

trait CanSetCurrency
{
    protected string|Closure|null $currency = null;

    protected function setCurrency(string|Closure|null $currency): void
    {
        if (is_null($currency)) {
            return;
        }
        $this->currency = $currency;
    }

    public function hasCurrency(): bool
    {
        return ! $this->missingCurrency();
    }

    public function missingCurrency(): bool
    {
        return is_null($this->currency);
    }

    public function getCurrency(): ?string
    {
        return $this->evaluate($this->currency);
    }
}
