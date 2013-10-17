<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class createResourceArchive extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'core:createResourceArchive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an archive of the specified collections resources.';

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
     * @return void
     */
    public function fire()
    {
        $collectionId = $this->argument('collection-id');

        // Ensure that the archive directory exists
        $directory = storage_path() . '/archives/' . $collectionId;

        if ( ! is_dir($directory) ) {
            mkdir( $directory, 0777, true );
        }

        // Ensure the PID directory exists
        $pidDirectory = $directory . '/pid';

        if ( ! is_dir($pidDirectory) ) {
            mkdir( $pidDirectory, 0777);
        }

        $pidFileName = $pidDirectory . '/' . getmypid();

        $pidFile = fopen( $pidFileName, 'w');
        fclose($pidFile);

        // Clean up PID files when we exit/are complete
        register_shutdown_function(function() use ($pidFileName) {
            unlink($pidFileName);
        });

        // We need to go through and kill other processes that are running
        $pidFiles = glob($pidDirectory . '/*');

        foreach ( $pidFiles as $_pidFile ) {
            $explode = explode('/', $_pidFile);
            $_pid = $explode[ count($explode) - 1];

            if ( $_pid  != getmypid() ) {
                posix_kill($_pid, 2);
                unlink($pidDirectory . '/' . $_pid);
            }
        }


        // Get all the catalogues (and resources that belong to them) that belong to this collection
        $catalogues = Catalogue::whereCollectionId($collectionId)->with( array('resources' => function($query) {
            $query->whereSync(1);
        }) )->get();

        // Make the zip archive
        $zip = new \ZipArchive;
        $zip->open( $directory . '/' . time() . '.zip', \ZipArchive::CREATE );

        foreach ( $catalogues as $catalogue ) {
            if ( isset($catalogue->resources) ) {
                
                $path = base_path() . '/resources/' . $collectionId . '/';

                foreach ( $catalogue->resources as $resource ) {
                    $zip->addFile( $path . $resource->filename, $resource->filename );
                }
            } 
        }

        $zip->close();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return array(
            array('collection-id', InputArgument::REQUIRED, 'The collection id.'),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array( );
    }

}