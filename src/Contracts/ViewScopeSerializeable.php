<?php

declare(strict_types=1);

namespace Honed\Table\Contracts;

interface ViewScopeSerializeable
{
    /**
     * Serialize the view scope for storage.
     */
    public function viewScopeSerialize(): string;
}
