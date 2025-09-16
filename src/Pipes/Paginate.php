<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Interpret;
use Honed\Core\Pipe;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @template TClass of \Honed\Table\Table
 *
 * @extends Pipe<TClass>
 */
class Paginate extends Pipe
{
    /**
     * Run the paginate logic.
     *
     * @throws InvalidArgumentException
     */
    public function run(): void
    {
        $instance = $this->instance;

        $paginate = $instance->getPaginate();

        $method = Str::camel($paginate);

        if (! method_exists($this, $method)) {
            throw new InvalidArgumentException(
                "The supplied paginator type [{$paginate}] is not supported."
            );
        }

        [$records, $pagination] = $this->{$method}(
            $instance->getBuilder(),
            $this->getValue($instance),
            $instance->getPageKey(),
            $instance->getWindow()
        );

        $instance->setRecords($records);
        $instance->setPagination($pagination);
    }

    /**
     * Get the pagination data for the length-aware paginator.
     *
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \Illuminate\Database\Eloquent\Model>  $paginator
     * @param  int  $window
     * @return array<string, mixed>
     */
    public function lengthAwarePagination($paginator, $window)
    {
        return array_merge($this->simplePagination($paginator), [
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
            'firstLink' => $paginator->url(1),
            'lastLink' => $paginator->url($paginator->lastPage()),
            'links' => $this->createPaginatorLinks($paginator, $window),
        ]);
    }

    /**
     * Get the pagination data for the simple paginator.
     *
     * @param  \Illuminate\Contracts\Pagination\Paginator<int, \Illuminate\Database\Eloquent\Model>  $paginator
     * @return array<string, mixed>
     */
    public function simplePagination($paginator)
    {
        return array_merge($this->cursorPagination($paginator), [
            'currentPage' => $paginator->currentPage(),
        ]);
    }

    /**
     * Get the pagination data for the cursor paginator.
     *
     * @param  \Illuminate\Pagination\AbstractCursorPaginator<int, \Illuminate\Database\Eloquent\Model>|\Illuminate\Contracts\Pagination\Paginator<int, \Illuminate\Database\Eloquent\Model>  $paginator
     * @return array<string, mixed>
     */
    public function cursorPagination($paginator)
    {
        return array_merge($this->collectionPagination($paginator), [
            'prevLink' => $paginator->previousPageUrl(),
            'nextLink' => $paginator->nextPageUrl(),
            'perPage' => $paginator->perPage(),
        ]);
    }

    /**
     * Get the value to be used for pagination.
     *
     * @param  TClass  $instance
     * @return int
     */
    protected function getValue($instance)
    {
        $options = $instance->getPerPage();

        if (! is_array($options)) {
            return $options;
        }

        $perPage = Interpret::int($instance->getRequest(), $instance->getRecordKey());

        if (! $perPage || ! in_array($perPage, $options)) {
            $perPage = $instance->getDefaultPerPage();
        }

        $instance->createPageOptions($options, $perPage);

        return $perPage;
    }

    /**
     * Use a simple paginator.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     * @param  int  $perPage
     * @param  string  $key
     * @return array{array<int, \Illuminate\Database\Eloquent\Model>, array<string, mixed>}
     */
    protected function simple($builder, $perPage, $key)
    {
        $paginator = $builder->simplePaginate($perPage, pageName: $key);

        return [$paginator->items(), $this->simplePagination($paginator)];
    }

    /**
     * Use a length aware paginator.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     * @param  int  $perPage
     * @param  string  $key
     * @param  int  $window
     * @return array{array<int, \Illuminate\Database\Eloquent\Model>, array<string, mixed>}
     */
    protected function lengthAware($builder, $perPage, $key, $window)
    {
        $paginator = $builder->paginate($perPage, pageName: $key)
            ->withQueryString();

        return [$paginator->items(), $this->lengthAwarePagination($paginator, $window)];
    }

    /**
     * Use a cursor paginator.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     * @param  int  $perPage
     * @param  string  $key
     * @return array{array<int, \Illuminate\Database\Eloquent\Model>, array<string, mixed>}
     */
    protected function cursor($builder, $perPage, $key)
    {
        $paginator = $builder->cursorPaginate($perPage, cursorName: $key)
            ->withQueryString();

        return [$paginator->items(), $this->cursorPagination($paginator)];
    }

    /**
     * Use a collection paginator.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $builder
     * @return array{array<int, \Illuminate\Database\Eloquent\Model>, array<string, mixed>}
     */
    protected function collection($builder)
    {
        $paginator = $builder->get();

        return [$paginator->all(), $this->collectionPagination($paginator)];
    }

    /**
     * Get the base metadata for the collection paginator.
     *
     * @param  \Illuminate\Support\Collection<int,\Illuminate\Database\Eloquent\Model>|\Illuminate\Pagination\AbstractCursorPaginator<int, \Illuminate\Database\Eloquent\Model>|\Illuminate\Contracts\Pagination\Paginator<int, \Illuminate\Database\Eloquent\Model>  $paginator
     * @return array<string, mixed>
     */
    protected function collectionPagination($paginator)
    {
        return [
            'empty' => $paginator->isEmpty(),
        ];
    }

    /**
     * Create pagination links with a sliding window around the current page.
     *
     * @param  \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, \Illuminate\Database\Eloquent\Model>  $paginator
     * @param  int  $window
     * @return array<int, array<string, mixed>>
     */
    protected function createPaginatorLinks($paginator, $window)
    {
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $onEachSide = $window;

        $start = max(1, min($currentPage - $onEachSide, $lastPage - ($onEachSide * 2)));
        $end = min($lastPage, max($currentPage + $onEachSide, ($onEachSide * 2 + 1)));

        return array_map(
            static fn (int $page) => [
                'url' => $paginator->url($page),
                'label' => (string) $page,
                'active' => $currentPage === $page,
            ],
            range($start, $end)
        );
    }
}
