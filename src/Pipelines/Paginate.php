<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Honed\Core\Interpret;
use Honed\Table\Table;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Paginate
{
    /**
     * Paginate the records.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @param  \Closure(Table<TModel, TBuilder>): Table<TModel, TBuilder>  $next
     * @return \Honed\Table\Table<TModel, TBuilder>
     */
    public function __invoke($table, $next)
    {
        $perPage = $this->perPage($table);

        $paginator = $table->getPaginator();
        $key = $table->getPageKey();
        $builder = $table->getBuilder();

        switch (true) {
            case $table->isLengthAware($paginator):
                $records = $builder->paginate($perPage, pageName: $key)
                    ->withQueryString();

                $table->setPaginationData($table->lengthAwarePaginator($records));
                $table->setRecords($records->items());

                break;
            case $table->isSimple($paginator):
                $records = $builder->simplePaginate($perPage, pageName: $key)
                    ->withQueryString();

                $table->setPaginationData($table->simplePaginator($records));
                $table->setRecords($records->items());

                break;
            case $table->isCursor($paginator):
                $records = $builder->cursorPaginate($perPage, cursorName: $key)
                    ->withQueryString();

                $table->setPaginationData($table->cursorPaginator($records));
                $table->setRecords($records->items());

                break;
            case $table->isCollector($paginator):
                $records = $builder->get();

                $table->setPaginationData($table->collectionPaginator($records));
                $table->setRecords($records->all());

                break;
            default:
                throw new \InvalidArgumentException(\sprintf(
                    'The provided paginator [%s] is invalid.',
                    $paginator
                ));
        }

        return $next($table);
    }

    /**
     * Get the per page value.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @return int
     */
    public function perPage($table)
    {
        $pagination = $table->getPagination();

        if (! \is_array($pagination)) {
            return $pagination;
        }

        $perPage = Interpret::int($table->getRequest(), $table->getRecordKey());

        if (\is_null($perPage) || ! \in_array($perPage, $pagination)) {
            $perPage = $table->getDefaultPagination();
        }

        $table->createRecordsPerPage($pagination, $perPage);

        return $perPage;
    }
}
