<?php

/**
 * Bioteaweb API
 *
 * A rest API frontend and indexer for the Biotea RDF project
 *
 * @link    http://biotea.idiginfo.org/api
 * @author  Casey McLaughlin <caseyamcl@gmail.com>
 * @license Copyright (c) Florida State University - All Rights Reserved
 */

// ------------------------------------------------------------------

namespace Bioteawebapi\Services;
use RuntimeException;
use RecursiveDirectoryIterator as RDI;
use RecursiveIteratorIterator;
use EasyRdf_Graph;

/** 
 * Provides file access to RDF Files and annotations
 * and resolves URL
 */
class RDFFileClient
{
    /**
     * @var string  Includes trailing slash
     */
    private $path;

    /**
     * @var string  Includes trailing slash
     */
    private $url;

    /**
     * @var string  Hardcoded filePatern to look for
     */
    private $filePattern = "/^PMC[\d]+\.rdf$/";

    /**
     * @var \RecursiveIteratorIterator
     */
    private $iterator;

    // --------------------------------------------------------------

    /**
     * Constructor
     *
     * @param string $rdfFilePath
     * @param string $rdfFileBaseUrl
     */
    public function __construct($rdfFilePath, $rdfFileBaseUrl)
    {
        $this->setBasePath($rdfFilePath);
        $this->setRdfFileBaseUrl($rdfFileBaseUrl);
        $this->resetFileIterator();
    }

    // --------------------------------------------------------------

    /**
     * Set the RDF File base URL
     *
     * Trailing slash will be auto-appended if not present
     *
     * @param string
     */
    public function setRdfFileBaseUrl($rdfFileBaseUrl)
    {
        if (substr($rdfFileBaseUrl, -1) != '/') {
            $rdfFileBaseUrl .= '/';
        }

        $this->url = $rdfFileBaseUrl;
    }

    // --------------------------------------------------------------

    /**
     * Set the RDF File base Path
     *
     * Trailing slash will be auto-appended if not present
     *
     * @param string
     */
    public function setBasePath($rdfFilePath)
    {
        if ( ! is_readable($rdfFilePath)) {
            throw new RuntimeException("RDF Filepath not readable: " . $rdfFilePath);
        }

        $this->path = realpath($rdfFilePath) . DIRECTORY_SEPARATOR;
    }

    // --------------------------------------------------------------

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->path;
    }

    // --------------------------------------------------------------

    /**
     * Get the full system path for a given path
     *
     * @return string
     */
    public function resolvePath($path)
    {
        if (strlen($path) > 0 && $path{0} == '/') {
            $path = substr($path, 1);
        }

        return $this->path . $path;
    }

    // --------------------------------------------------------------

    /**
     * Get the URL for a given path
     *
     * @return string
     */
    public function resolveUrl($path)
    {
        if (strlen($path) > 0 && $path{0} == '/') {
            $path = substr($path, 1);
        }

        return $this->url . $path;       
    }

    // --------------------------------------------------------------

    /**
     * Get a MD5 Hash of the RDF file and its associated Annotation files
     *
     * Takes a file checksum of each file, then smashes that string
     * together and takes a checksum of that
     *
     * @param  string $path  Relative path to the main RDF file
     * @return string
     */
    public function getFilesMD5($path)
    {
        $files = array();
        $md5s = array();

        //Get full filepaths
        $files[] = $this->resolvePath($path);
        foreach($this->getAnnotationFiles($path, true) as $apath) {
            $files[] = $apath;
        }

        //Get MD5 checksums
        foreach($files as $file) {
            if ($val = @md5_file($file)) {
                $md5s[] = $val;
            }
        }

        return md5(implode('', $md5s));
    }

    // --------------------------------------------------------------

    /**
     * Get annotation files for a RDF path
     *
     * Does not do any checks to see if the files actually exist
     *
     * @param string $path        Relative path to RDF file
     * @param boolean $fullPaths  If true, will send fullpaths, else relative paths
     * @return array
     */
    public function getAnnotationFiles($path, $fullPaths = false)
    {
        //Get the fullpath to the file
        $path = $this->resolvePath($path);

        //Resolve the basename and the directory name
        $basename = basename($path, '.' . pathinfo($path, PATHINFO_EXTENSION)); 
        $dirname  = dirname($path);

        //Get the full paths for any annotation files
        //(hardcoded for now - perhaps send in as parameters)
        $arr = array(
            'ncbo'     => $dirname . '/AO_annotations/' . $basename . '_ncboAnnotator.rdf',
            'whatizit' => $dirname . '/Bio2RDF/' . $basename . '_whatizitUkPmcAll.rdf'
        );

        //Change full paths to relative paths if we want that info
        if ( ! $fullPaths) {
            foreach ($arr as $k => $item) {
                $arr[$k] = substr($item, strlen($this->getBasePath()));
            }
        }

        //Return
        return $arr;
    }

    // --------------------------------------------------------------

    /**
     * Count the triples in an RDF fileset (or single fiel)
     *
     * @TODO: MOVE This into its own class??
     *
     * @param string         $path
     * @param boolean        $includeAnnots
     * @param \EasyRDF_graph $graph          Optional (auto-constructs)     
     * @return int
     */
    public function countTriples($path, $includeAnnots = true, EasyRdf_Graph $graph = null)
    {   
        //Optional runtime dependency injection
        if ( ! $graph) {
            $graph = new EasyRdf_Graph();
        }

        $num = 0;
        $graph->parseFile($this->resolvePath($path));
        $num = $graph->countTriples();

        if ($includeAnnots) {
            foreach($this->getAnnotationFiles($path, true) as $filePath) {
                $graph->parseFile($filePath);
                $num += $graph->countTriples();
            }
        }

        return $num;
    }

    // --------------------------------------------------------------

    /**
     * Recursively scans directories starting at basepath, and returns
     * count of files matching pattern
     *
     * Note: since annotation RDF files are treated as being associated
     * with the main RDF files, only the main RDF files are counted
     *
     * @return int
     */
    public function countRdfFiles()
    {
        $iterator = new RecursiveIteratorIterator(new RDI($this->path));

        //Count
        $count = 0;

        do {
            $count++;
        } while ($this->doGetNextFile($iterator));

        return $count;
    } 

    // --------------------------------------------------------------

    /**
     * Resets the file iterator
     */
    public function resetFileIterator()
    {
        $this->iterator = new RecursiveIteratorIterator(new RDI($this->path));
    }

    // --------------------------------------------------------------

    /**
     * Get the next file using the interator
     *
     * @param  boolean $fullpath  If true, returns the fullpath to the file
     * @return boolean|string  Path to file, relative to basepath,
     *                         or false if no more files
     */
    public function getNextFile($fullPath = false)
    {
        $filename = $this->doGetNextFile($this->iterator);

        if ($filename && $fullPath) {
            $filename = $this->resolvePath($filename);
        }

        return $filename;
    }

    // --------------------------------------------------------------

    /**
     * Private interface for get the next file using the iterator
     *
     * @return boolean|string  Path to file, relative to basepath,
     *                         or false if no more files
     */
    public function doGetNextFile($iterator)
    {
        $path = false;

        //Get the next file, and see if it matches the regex pattern
        //otherwise, do it again until false or a valid regex
        while ($iterator->valid() && $path === false) {

            $fileName = $iterator->getFileName();
            if (preg_match($this->filePattern, $fileName)) {
                
                //Get the full and relative paths
                $fullPath = dirname($iterator->getPathName()) . DIRECTORY_SEPARATOR;
                $relPath  = substr($fullPath, strlen($this->path)) . $fileName;
                $fullPath = $fullPath . $fileName;

                //Build the object
                $path = $relPath;
            }

            //Next item
            $iterator->next();
        }
       
       return $path;
    }
}

/* EOF: RDFFileClient.php */