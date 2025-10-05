<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Screenshot;
use App\Models\Tag;
use Illuminate\Support\Facades\File;

class ImportScreenshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'screenshots:import {path=storage/app/public/screenshots}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import screenshots from a folder into the database with tags';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $basePath = base_path($this->argument('path'));

        if (!File::exists($basePath)) {
            $this->error("Path does not exist: $basePath");
            return Command::FAILURE;
        }

        $files = File::allFiles($basePath);

        foreach ($files as $file) {
            $relativePath = str_replace(base_path('storage/app/public/'), '', $file->getPathname());

            // Normalize slashes (Windows safe)
            $relativePath = str_replace('\\', '/', $relativePath);

            // Skip if already imported
            if (Screenshot::where('file_path', $relativePath)->exists()) {
                continue;
            }

            // Save screenshot
            $screenshot = Screenshot::create([
                'title'     => $file->getFilenameWithoutExtension(),
                'file_path' => $relativePath,
            ]);

            // Extract folder names as tags
            $parts = explode('/', dirname($relativePath)); // e.g. ["screenshots","2024_live","Jan","Monday","5_min","Bullish"]
            $tagNames = array_slice($parts, 1); // skip "screenshots"

            $tagIds = collect($tagNames)->map(fn($name) => Tag::firstOrCreate(['name' => $name])->id);
            $screenshot->tags()->sync($tagIds);
        }

        $this->info("Imported " . count($files) . " screenshots.");
        return Command::SUCCESS;
    }
}
