<?php

declare(strict_types=1);

namespace Honed\Table\Http\Requests;

use Honed\Action\Http\Requests\ActionRequest;
use Honed\Table\Http\Controllers\TableController;

class TableRequest extends ActionRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string,array<int,mixed>>
     */
    public function rules(): array
    {
        return \array_merge(parent::rules(), [
            TableController::TableKey => ['required'],
        ]);
    }
}
