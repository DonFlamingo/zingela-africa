<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateIconsCommand extends Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'update:icons';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';


    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $paths = [];

        # Rotating icons
        $files = File::allFiles(base_path('images/device_icons/rotating'));
        foreach ($files as $file)
        {
            if (!is_object($file) || empty($file->getFilename()))
                continue;

            list($width, $height) = getimagesize($file);
            if (!$width || !$height)
                continue;

            $paths[] = 'frontend/images/device_icons/rotating/' . $file->getFilename();
        }

        if ( $paths ) {
            DB::table('device_icons')->whereIn('path', $paths)->where('type', '!=', 'rotating')->update([
                'type' => 'rotating'
            ]);
        }

        $this->line("Job done[OK]\n");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array();
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array();
    }
}
