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
     * The directory where archives are stored
     */
    protected $directory;

    /**
     * The directory where PID files are stored
     */
    protected $pidDirectory;

    /**
     * The collection ID of the archive currently being created
     */
    protected $collectionId;

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
        $this->collectionId = $this->argument('collection-id');
        $this->directory = storage_path() . '/archives/' . $this->collectionId;
        $this->pidDirectory = $this->directory . '/pid';

        if ( ! is_dir($this->directory) ) {
            mkdir( $this->directory, 0777, true );
        }

        // Ensure the PID directory exists
        if ( ! is_dir($this->pidDirectory) ) {
            mkdir( $this->pidDirectory, 0777);
        }

        $pidFileName = $this->pidDirectory . '/' . getmypid();

        $pidFile = fopen( $pidFileName, 'w');
        fclose($pidFile);

        // Clean up PID files when we exit/are complete
        register_shutdown_function(function() use ($pidFileName) {
            unlink($pidFileName);
        });

        $this->stopOtherArchives();

        // Get all the catalogues (and resources that belong to them) that belong to this collection
        $catalogues = Catalogue::whereCollectionId($this->collectionId)->with( array('resources' => function($query) {
            $query->whereSync(1);
        }) )->get();

        // Make the zip archive
        $zip = new \ZipArchive;
        $zip->open( $this->directory . '/' . time() . '.zip', \ZipArchive::CREATE );

        foreach ( $catalogues as $catalogue ) {
            if ( isset($catalogue->resources) ) {
                
                $path = base_path() . '/resources/' . $this->collectionId . '/';

                foreach ( $catalogue->resources as $resource ) {
                    if ( is_file($path . $resource->filename) ) {
                        $zip->addFile( $path . $resource->filename, $resource->filename );
                    }
                }
            } 
        }

        $zip->close();

        $this->cleanUp();
    }

    /**
     * Stop any other archive actions that might be occuring for this collection
     */
    protected function stopOtherArchives()
    {
        $pidFiles = glob($this->pidDirectory . '/*');

        foreach ( $pidFiles as $_pidFile ) {
            $explode = explode('/', $_pidFile);
            $_pid = $explode[ count($explode) - 1];

            if ( $_pid  != getmypid() ) {
                posix_kill($_pid, 2);
                unlink($this->pidDirectory . '/' . $_pid);
            }
        }
    }

    /**
     * Clean up the current archives
     */
    protected function cleanUp()
    {
        $archives = glob($this->directory . '/*.zip');
        rsort($archives);
        unset($archives[0], $archives[1]);

        if ( count($archives) ) {
            foreach ( $archives as $archive ) {
                unlink($archive);
            }
        }
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