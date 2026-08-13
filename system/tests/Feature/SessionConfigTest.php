<?php

it('keeps a 7-day session lifetime by default', function () {
    expect(config('session.lifetime'))->toBe(10080);
});
