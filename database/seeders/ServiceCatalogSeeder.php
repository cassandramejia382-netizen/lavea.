<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ServiceCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = json_decode(File::get(database_path('seeders/service-catalog.json')), true, flags: JSON_THROW_ON_ERROR);

        foreach ($services as $service) {
            if ($service['image'] === null) {
                continue;
            }

            $source = database_path('seeders/service-images/'.basename($service['image']));

            if (! File::isFile($source)) {
                throw new RuntimeException('Missing bundled image for '.$service['service_name']);
            }

            if (! Storage::disk('public')->exists($service['image'])
                && ! Storage::disk('public')->put($service['image'], File::get($source))) {
                throw new RuntimeException('Unable to store the image for '.$service['service_name']);
            }
        }

        DB::transaction(function () use ($services): void {
            foreach ($services as $service) {
                Service::updateOrCreate(['service_name' => $service['service_name']], $service);
            }
        });
    }
}
