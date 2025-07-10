<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Action\Contracts\Action;
use Honed\Table\Table;
use Illuminate\Contracts\Translation\Translator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class ViewAction implements Action
{
    public const FIELD = 'name';

    /**
     * The translator instance.
     */
    protected Translator $translator;

    /**
     * Create a new action instance.
     */
    public function __construct()
    {
        $this->translator = app(Translator::class);
    }

    /**
     * Get the name of the view from a request.
     */
    protected function getName(Request $request, ?string $field = null): string
    {
        $field = $this->field($field);

        Validator::make($request->all(), [
            $field => ['required', 'string', 'max:255'],
        ])->validate();

        /** @var string */
        return $request->input($field);
    }

    /**
     * Get the name of the name field from the request, enforcing the field name.
     */
    protected function field(?string $field): string
    {
        return $field ?? self::FIELD;
    }

    /**
     * Create the state of the view from the request.
     *
     * @return array<string, mixed>
     */
    protected function state(Table $table, Request $request): array
    {
        $referer = $request->header('referer');

        if (is_string($referer)) {
            $request = Request::create($referer, Request::METHOD_GET);
        }

        $table->define(); // @TODO

        return $table->request($request)->toState();
    }

    /**
     * Fail the action.
     *
     * @throws ValidationException
     */
    protected function fail(string $field, string $message): void
    {
        throw ValidationException::withMessages([
            $field => $message,
        ]);
    }

    /**
     * Fail the action due to the table not being viewable.
     *
     * @throws ValidationException
     */
    protected function invalid(?string $field = null, ?string $action = null): void
    {
        $field = $this->field($field);

        $this->fail($field,
            $this->translator->get('table::messages.view.missing', [
                'action' => $action ?? 'access',
                'attribute' => $field,
            ])
        );
    }

    /**
     * Fail the action due to the view name not being unique.
     *
     * @throws ValidationException
     */
    protected function notUnique(?string $field = null): void
    {
        $field = $this->field($field);

        $this->fail($field, $this->translator->get('table::messages.view.name.unique', [
            'attribute' => $field,
        ]));
    }
}
