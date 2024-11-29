<?php

use Honed\Table\Actions\BulkAction;

beforeEach(function () {
    $this->confirmable = BulkAction::make('test');
});