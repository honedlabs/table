<?php

namespace Honed\Table\Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class FilterRequest extends RequestFactory
{
    public function definition(): array
    {
        return [
            'email' => $this->faker->email,
        ];
    }
}
