<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\Column;
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
    public function toggle($columns)
    {
        if (! $this->canToggle()) {
            return $columns;
        }

        /** @var \Illuminate\Http\Request */
        $request = $this->getRequest();

        // Get the names of the columns from the query parameter
        $names = $this->getColumnsFromRequest($request);

        if ($this->canRemember()) {
            $names = $this->configureCookie($request, $names);
        }

        return Arr::where(
            $columns,
            fn (Column $column) => $column
                ->active($column->isDisplayed($names))
                ->isActive()
        );
    }

    /**
     * Use the columns cookie to determine which columns are active, or set the
     * cookie to the current columns.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array<int,string>|null  $params
     * @return array<int,string>|null
     */
    public function configureCookie($request, $params)
    {

        // If there are params, overwrite the cookie
        if (! \is_null($params)) {
            Cookie::queue(
                $this->getCookie(),
                \json_encode($params),
                $this->getDuration()
            );

            return $params;
        }

        $value = $request->cookie($this->getCookie(), '');

        if (! \is_string($value)) {
            return $params;
        }

        /** @var array<int,string>|null */
        return \json_decode($value, false);
    }

    /**
     * Retrieve the columns to display from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<int,string>|null
     */
    public function getColumnsFromRequest($request)
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
