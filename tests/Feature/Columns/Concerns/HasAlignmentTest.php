<?php

declare(strict_types=1);

use Honed\Table\Columns\Column;

beforeEach(function () {
    $this->column = Column::make('name');
});

it('has alignment', function () {
    expect($this->column)
        ->getAlignment()->toBe(Column::ALIGN_LEFT)
        ->align(Column::ALIGN_CENTER)->toBe($this->column)
        ->getAlignment()->toBe(Column::ALIGN_CENTER)
        ->alignment(Column::ALIGN_RIGHT)->toBe($this->column)
        ->getAlignment()->toBe(Column::ALIGN_RIGHT)
        ->alignCenter()->toBe($this->column)
        ->getAlignment()->toBe(Column::ALIGN_CENTER)
        ->alignRight()->toBe($this->column)
        ->getAlignment()->toBe(Column::ALIGN_RIGHT)
        ->alignLeft()->toBe($this->column)
        ->getAlignment()->toBe(Column::ALIGN_LEFT);
});
