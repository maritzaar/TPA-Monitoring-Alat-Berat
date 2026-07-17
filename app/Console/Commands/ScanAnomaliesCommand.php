<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('monitoring:scan-anomalies')]
#[Description('Scan for operational anomalies like high idle percentage')]
class ScanAnomaliesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting anomaly scan...');

        // Cari data dengan persen_idle > 50 dalam 30 hari terakhir agar relevan
        $anomalies = \App\Models\DataAlat::where('persen_idle', '>', 50)
            ->where('tanggal', '>=', now()->subDays(30))
            ->get();

        if ($anomalies->isEmpty()) {
            $this->info('No anomalies found.');
            return;
        }

        $admins = \App\Models\User::where('role', 'admin')->get();
        if ($admins->isEmpty()) {
            $this->error('No admin users found to receive notifications.');
            return;
        }

        $count = 0;
        foreach ($anomalies as $alat) {
            // Check if notification for this specific date and asset already exists to prevent spam
            $notificationExists = \Illuminate\Support\Facades\DB::table('notifications')
                ->where('type', \App\Notifications\AnomalyDetectedNotification::class)
                ->where('data', 'LIKE', '%"id_aset":"' . $alat->id_aset . '"%')
                ->where('data', 'LIKE', '%"tanggal":"' . \Carbon\Carbon::parse($alat->tanggal)->format('Y-m-d') . '%')
                ->exists();

            if (!$notificationExists) {
                $message = "Peringatan Anomali! Unit {$alat->id_aset} memiliki tingkat idle yang sangat tinggi (" . number_format($alat->persen_idle, 1) . "%) pada tanggal " . \Carbon\Carbon::parse($alat->tanggal)->format('d M Y');
                
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AnomalyDetectedNotification($alat, $message));
                $count++;
            }
        }

        $this->info("Scan complete. Sent {$count} new anomaly notifications.");
    }
}
