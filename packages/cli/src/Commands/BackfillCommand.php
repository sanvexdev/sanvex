<?php

namespace Sanvex\Cli\Commands;

use Illuminate\Console\Command;
use Sanvex\Core\SanvexManager;
use Sanvex\Core\Tenancy\Owner;

class BackfillCommand extends Command
{
    protected $signature = 'sanvex:backfill
                            {driver : The driver to backfill}
                            {--owner-type= : Owner type for tenant-scoped backfill}
                            {--owner-id= : Owner id for tenant-scoped backfill}';
    protected $description = 'Backfill existing data from an external API into sv_entities';

    public function handle(SanvexManager $connector): int
    {
        $driverId = $this->argument('driver');
        $owner = Owner::fromTypeAndId($this->option('owner-type'), $this->option('owner-id'));

        try {
            $connector->for($owner)->resolveDriver($driverId);
        } catch (\Throwable $e) {
            $this->error("Driver [{$driverId}] is not registered.");
            return self::FAILURE;
        }

        $scope = $owner->isGlobal()
            ? 'global/default'
            : $owner->type().'/'.$owner->id();

        $this->info("Starting backfill for [{$driverId}] owner [{$scope}]...");
        $this->warn("Note: Implement driver-specific backfill logic in the driver's backfill handler.");
        $this->info("Backfill complete.");
        return self::SUCCESS;
    }
}
