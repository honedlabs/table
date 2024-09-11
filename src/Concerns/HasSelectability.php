<?php

namespace Conquest\Table\Concerns;

use Closure;
use Conquest\Table\Columns\BaseColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait HasSelectability
{
    /**
     * @var bool|Closure<bool>
     */
    protected bool|Closure $selectable;

    /**
     * @param  bool|Closure<bool>|null  $selectable
     */
    protected function setSelectable(bool|Closure|null $selectable): void
    {
        if (is_null($selectable)) {
            return;
        }
        $this->selectable = $selectable;
    }

    /**
     * @internal
     * @return bool|Closure<bool>
     */
    protected function definedSelectable(): bool|Closure
    {
        if (isset($this->selectable)) {
            return $this->selectable;
        }

        if (method_exists($this, 'selectable')) {
            return $this->selectable();
        }

        return true;
    }

    /**
     * @param Model $model
     * @return bool
     */
    public function isSelectable(Model $model): bool
    {
        $rule = $this->definedSelectable();

        if (is_bool($rule)) {
            return $rule;
        }

        return $this->evaluate(
            $rule,
            [
                'model' => $model,
                'modelName' => $model,
                'record' => $model,
            ],
            [
                class_basename($model) => $model,
                Model::class => $model,
            ]
        );
    }
}
