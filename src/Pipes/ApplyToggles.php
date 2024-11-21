<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Closure;
use Honed\Table\Pipes\Contracts\Toggles;
use Honed\Table\Table;

/**
 * @internal
 */
class ApplyToggles implements Toggles
{
    public function handle(Table $table, Closure $next)
    {
        return $next($table);
    }

    // private function getToggledColumns(): array
    // {
    //     $cols = request()->query($this->getToggleKey(), null);
    //     return (is_null($cols)) ? [] : explode(',', $cols);
    // }

    // private function applyToggleability(): void
    // {
    //     // If it isn't toggleable then dont do anything
    //     if (!$this->isToggleable()) return;

    //     $cols = $this->getToggledColumns();

    //     if ($this->hasRememberKey() && empty($cols)) {
    //         // Use the remember key to get the columns
    //         $cols = json_decode(request()->cookie($this->getRememberKey(), []));
    //     }

    //     if (empty($cols)) {
    //         // If there are no columns, then set the default columns
    //         return;
    //     }

    //     foreach ($this->getColumns() as $column) {
    //         if (in_array($column->getName(), $cols)) $column->active(true);
    //         else $column->active(false);
    //     }

    //     if ($this->hasRememberKey()) {
    //         Cookie::queue($this->getRememberKey(), json_encode($cols), $this->getRememberDuration());
    //     }
    // }
}
