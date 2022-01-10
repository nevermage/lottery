<?php

namespace App\Console\Commands;

use App\Services\LotService;
use Illuminate\Console\Command;

class RollWinner extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:roll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rolls a random winner for lots and sends email notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        LotService::rollWinner();
        return 0;
    }
}
