<?php

namespace Bioteawebapi\Commands;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TaskTracker\OutputHandler\SymfonyConsole as TrackerConsoleHandler;
use TaskTracker\Tracker;
use ZipArchive;

/**
 * Class to build ZIP Files from RDF directory
 */
class BuildZipFiles extends Command
{
    /**
     * @var int  Default batch size
     */
    private $defaultBatchSize = 10000;

    /**
     * @var string  Default File Prefix
     */
    private $defaultPrefix = 'biotea_rdfs_';

    /**
     * @var TaskTracker\Tracker
     */
    private $tracker;

    /**
     * @var string  Output file prefix
     */
    private $outFilePathPrefix;

    /**
     * @var int  Tracks the number of output files
     */
    private $numOutputFiles = 0;

    // --------------------------------------------------------------

    protected function configure()
    {
        $this->setName('buildzips')->setDescription('Build ZIP files and deposit them on the filesystem');
        $this->addArgument('path', InputArgument::REQUIRED, 'Output path to place ZIP files');
        $this->addOption('quiet', 'q', InputOption::VALUE_NONE, 'Quiet supresses output to the console');
        $this->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Optional limit', 0);

        $this->addOption(
            'prefix', 'p',
            InputOption::VALUE_REQUIRED,
            sprintf('Optional prefix (default is %s)', $this->defaultPrefix),
            $this->defaultPrefix
        );

        $this->addOption(
            'batchsize', 'b',
            InputOption::VALUE_REQUIRED, 
            sprintf("The number of rdf files per ZIP (default %s)", number_format($this->defaultBatchSize, 0)),
            $this->defaultBatchSize
        );
    }        

    // --------------------------------------------------------------

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Check ZIP functionality
        if ( ! class_exists('\ZipArchive')) {
            $url = "http://www.php.net/manual/en/zip.installation.php";
            throw new \RuntimeException("PHP is not compiled to support ZIP files!  See " . $url);
        }

        //Check output path
        $outPath = $input->getArgument('path');
        if ( ! is_writeable($outPath)) {
            throw new \InvalidArgumentException("Path is not writable or does not exist: " . $outPath);
        }

        if (substr($outPath, -1) != DIRECTORY_SEPARATOR) {
            $outPath .= DIRECTORY_SEPARATOR;
        } 

        //Get the limit and batch size
        $limit     = (int) $input->getOption('limit');
        $batchSize = (int) $input->getOption('batchsize');
        $quiet     = (boolean) $input->getOption('quiet');

        //Setup task tracker
        $this->setupTaskTracker($limit, $quiet, $output);

        //Setup the output filename
        $this->outFilePathPrefix = $outPath . $input->getOption('prefix');

        //Get the total number of files...
        $totalNumFiles = $this->app['fileclient']->countRdfFiles();

        //Reset the file iterator...
        $this->app['fileclient']->resetFileIterator();
        
        //Main loop
        for ($batch = array(), $i = 0; $i < $totalNumFiles; $i++) {

            //Filename and full filename
            $filename = $this->app['fileclient']->getNextFile();

            //If we're over the limit or there is no filename
            if ( ! $filename OR ($limit && $i > $limit)) {
                break;
            }

            //Build a batchset
            $batchset = array_merge(
                array($filename => $this->app['fileclient']->resolvePath($filename)),
                array_combine(
                    $this->app['fileclient']->getAnnotationFiles($filename, false),
                    $this->app['fileclient']->getAnnotationFiles($filename, true)
                )
            );

            //Add it to the batch
            $batch[$filename] = $batchset;

            //Build an archive
            if (count($batch) % $batchSize == 0) {

                $this->tracker->tick(1, "Building ZIP file");

                $this->buildArchive($batch);
                $batch = array();
            }
            else {
                $this->tracker->tick(1, "Preparing files");
            }

        }

        //If leftovers after loop
        if (count($batch) > 0) {
            $this->buildArchive($batch);
        }

        //Delete any leftover files from old runs
        $numDeleted = $this->deleteOldFiles();

        $this->tracker->finish(
            sprintf('Created %s ZIP files', number_format($this->numOutputFiles, 0))
        );
    }

    // --------------------------------------------------------------

    /**
     * Setup Task Tracker
     *
     * @param int $limit
     */
    private function setupTaskTracker($limit, $quiet, $output)
    {
        //Output to log
        //$trackerHandlers = array(new TrackerMonologHandler($this->app['monolog'], 60));
        $trackerHandlers = array();

        //Also output to console unless quiet is set
        if ( ! $quiet) {
            $trackerHandlers[] = new TrackerConsoleHandler($output);
        }

        //Setup a task tracker
        $this->tracker = new Tracker($trackerHandlers);
    }

    // --------------------------------------------------------------

    /**
     * Delete any old files after run
     *
     * @return int  Number of files deleted
     */
    private function deleteOldFiles()
    {
        //If nothing new added, don't delete anything old
        if ($this->numOutputFiles == 0) {
            return 0;
        }

        $numDeleted = 0;

        $currNum = $this->numOutputFiles + 1;
        while (file_exists($this->outFilePathPrefix . $currNum)) {

            unlink($this->outFilePathPrefix . $currNum);
            $numDeleted++;

            $currNum++;
        }

        return $numDeleted;
    }
 
    // --------------------------------------------------------------

    /**
     * Build a ZIP archive
     *
     * @param  array  $fileList         Full paths
     * @return int    Filesize in bytes of new file
     */
    private function buildArchive(Array $batch)
    {  
        //Increment to the next output file
        $this->numOutputFiles++;

        //Build the output file name
        $outFilePath = $this->outFilePathPrefix . $this->numOutputFiles . '.zip';

        //Open the ZIP file for writing
        $mode = (file_exists($outFilePath)) ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE;
        $zipFile = new ZipArchive();
        $result = $zipFile->open($outFilePath, $mode);

        //..or Exception
        if ( ! $result) {
            throw new \Exception("Could not create ZIP archive at: " . $outFilePath);
        }

        //Add the files
        foreach($batch as $set) {

            //Foreach file in the set
            foreach($set as $filePath => $fullPath) {
                if  ( ! $zipFile->addFile($fullPath, $filePath)) {
                    throw new \Exception("Error adding %s to ZIPfile %s", $filePath, $outFilePath);
                }    
            }
        }

        //Close it up
        $zipFile->close();

        //Return the filesize
        return filesize($outFilePath);
    }
}

/* EOF: BuildZipFiles.php */