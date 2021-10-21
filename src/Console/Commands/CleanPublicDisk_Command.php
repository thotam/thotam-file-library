<?php

namespace Thotam\ThotamFileLibrary\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Thotam\ThotamFileLibrary\Models\FileLibrary;

class CleanPublicDisk_Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'thotam-file-library:clean-public-disk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dọn dẹp bộ nhớ khi file đã được upload lên Google Drive';

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
        $FileLibrarys = FileLibrary::where('active', true)
                   ->where('drive', 'google')
                   ->whereNull('cleaned')
                   ->limit(10000)
                   ->get();

        foreach ($FileLibrarys as $key => $file) {
            $check = true;

            if ($file->youtube != NULL && $file->youtube_id == NULL) {
                $check = false;
            }

            if ($file->vimeo != NULL && $file->vimeo_id == NULL) {
                $check = false;
            }

            if ($file->google_id == NULL) {
                $check = false;
            }

            if ($check) {
                if (Storage::disk('public')->exists($file->local_path)) {
                    if (Storage::disk('public')->delete($file->local_path)) {
                        $file->update([
                            'cleaned' => true,
                        ]);
                    }
                } else {
                    $file->update([
                        'cleaned' => true,
                    ]);
                }
            }
        }

        return 0;
    }
}
