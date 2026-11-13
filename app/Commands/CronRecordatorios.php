<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Controllers\Cron;

class CronRecordatorios extends BaseCommand
{
    protected $group       = 'Cron';
    protected $name        = 'cron:recordatorios';
    protected $description = 'Envía recordatorios de devolución automáticos';

    public function run(array $params)
    {
        CLI::write('Iniciando envío de recordatorios...', 'yellow');
        
        $cron = new Cron();
        $cron->recordatorios();
        
        CLI::write('Proceso completado.', 'green');
    }
}