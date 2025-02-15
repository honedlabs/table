<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\Column;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;

trait HasToggle
{
    use Support\CanRemember;
    use Support\CanToggle;
    use Support\ColumnsKey;
    use Support\HasCookie;
    use Support\HasDuration;

    /**
     * Toggle the columns that are visible.
     *
     * @param  array<int,\Honed\Table\Columns\Column>  $columns
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public function toggle(array $columns): array
    {
        if (! $this->canToggle()) {
            return $columns;
        }

        /** @var \Illuminate\Http\Request */
        $request = $this->getRequest();

        $activeColumns = $this->getColumnsFromRequest($request);

        if ($this->canRemember()) {
            $activeColumns = $this->configureCookie($request, $activeColumns);
        }

        return Arr::where(
            $columns,
            fn (Column $column) => $column
                ->active($column->isDisplayed($activeColumns))
                ->isActive()
        );
    }

    /**
     * Use the columns cookie to determine which columns are active, or set the
     * cookie to the current columns.
     *
     * @param  array<int,string>|null  $params
     * @return array<int,string>|null
     */
    public function configureCookie(Request $request, ?array $params): ?array
    {
        if (\is_null($params)) {
            Cookie::queue($this->getCookie(), $params, $this->getDuration());

            return $params;
        }

        /** @var array<int,string>|null */
        return $request->cookie($this->getCookie());
    }

    /**
     * Retrieve the columns to display from the request.
     *
     * @return array<int,string>|null
     */
    public function getColumnsFromRequest(Request $request): ?array
    {
        $matches = $request->string($this->getColumnsKey(), null);

        if ($matches->isEmpty()) {
            return null;
        }

        /** @var array<int,string> */
        return $matches
            ->explode(',')
            ->map(fn ($value) => trim($value))
            ->filter()
            ->toArray();
    }
}
