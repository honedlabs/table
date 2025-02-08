<?php

declare(strict_types=1);

use Honed\Core\Contracts\HigherOrder;
use Honed\Core\Contracts\ProxiesHigherOrder;
use Honed\Table\Confirm\Concerns\Confirmable;
use Honed\Table\Confirm\Confirm;
use Honed\Table\Confirm\Proxies\HigherOrderConfirm;
use Honed\Core\Primitive;
use Illuminate\Support\Facades\URL;

class HigherOrderConfirmTest extends Primitive implements ProxiesHigherOrder
{
    use Confirmable;

    public static function make(): static
    {
        return new static;
    }

    public function __get(string $property): HigherOrder
    {
        return match ($property) {
            'confirm' => new HigherOrderConfirm($this),
            default => parent::__get($property),
        };
    }

    public function toArray(): array
    {
        return [];
    }
}

beforeEach(function () {
    $this->test = new HigherOrderConfirmTest;
});

it('proxies calls to the Confirm object', function () {
    expect($this->test->confirm->title('Title'))
        ->toBeInstanceOf(HigherOrderConfirmTest::class)
        ->getConfirm('title')->toBe('Title');
});

