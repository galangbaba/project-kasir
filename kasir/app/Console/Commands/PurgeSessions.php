<?php

namespace App\Console\Commands;

use App\Support\Security\SessionService;
use Illuminate\Console\Command;

class PurgeSessions extends Command
{
    protected $signature = 'sessions:purge';
    protected $description = 'Tandai sesi login kedaluwarsa dan hapus sesi lama';

    public function handle(SessionService $sessions): int
    {
        $this->info('Sesi lama dihapus: ' . $sessions->purge());

        return self::SUCCESS;
    }
}
