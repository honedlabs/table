<?php

use Honed\Table\Url\Url;

beforeEach(function () {
    $this->url = Url::make();
});

it('is not download by default', function () {
    expect($this->url->isDownload())->toBeFalse();
    expect($this->url->isNotDownload())->toBeTrue();
});

it('can be set to download', function () {
    expect($this->url->download())->toBeInstanceOf(Url::class)
        ->isDownload()->toBeTrue();
});

it('can be set to not download', function () {
    expect($this->url->download(false))->toBeInstanceOf(Url::class)
        ->isDownload()->toBeFalse();
});

it('can be set using setter', function () {
    $this->url->setDownload(true);
    expect($this->url->isDownload())->toBeTrue();
});

it('does not accept null values', function () {
    $this->url->setDownload(null);
    expect($this->url->isDownload())->toBeFalse();
});
