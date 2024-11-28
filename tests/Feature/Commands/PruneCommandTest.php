<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Laravel\Pulse\Facades\Pulse;

it('prune Pulse data', function () {
    # Arrange
    Date::setTestNow('2000-01-01 00:00:04');
    Pulse::set('type', 'foo', 'value');
    Date::setTestNow('2000-01-01 00:00:05');
    Pulse::set('type', 'bar', 'value');
    Date::setTestNow('2000-01-01 00:00:06');
    Pulse::set('type', 'baz', 'value');
    Pulse::ingest();

    Pulse::stopRecording();
    Date::setTestNow('2000-01-08 00:00:05');

    # Act
    Artisan::call('pulse:prune');

    # Assert
    expect(DB::table('pulse_values')->count())->toBe(0);
    expect(DB::table('pulse_entries')->count())->toBe(0);
    expect(DB::table('pulse_aggregates')->count())->toBe(0);
});

it('prune entries from the suggested before hours', function () {
    // Entries will be pruned
    Date::setTestNow('2000-01-01 00:00:04');
    Pulse::record('foo', 'xxxx', 1);
    Date::setTestNow('2000-01-01 00:00:05');
    Pulse::record('bar', 'xxxx', 1);

    // Entries will be kept
    Date::setTestNow("2000-01-07 00:00:00");
    Pulse::record('baz', 'xxxx', 1);
    Pulse::ingest();

    Pulse::stopRecording();
    Date::setTestNow('2000-01-09 00:00:00');

    # Act
    Artisan::call("pulse:prune --hours=168");

    expect(DB::table('pulse_entries')->pluck('type')->all())->toBe(['baz']);
});
