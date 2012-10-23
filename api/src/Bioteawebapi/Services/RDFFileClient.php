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
        if ($path{0} == '/') {
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
        if ($path{0} == '/') {
            $path = substr($path, 1);
        }

        return $this->url . $path;       
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
        $path = $this->resolvePath($path);

        $filename = basename($path, '.' . pathinfo($path, PATHINFO_EXTENSION)); 
        $dirname  = dirname($path);

        //See if we have associated annotation files
        //(hardcoded for now - perhaps send in as parameters)
        $arr = array(
            'ncbo'     => $dirname . '/AO_annotations/' . $filename . '_ncboAnnotator.rdf',
            'whatizit' => $dirname . '/Bio2RDF/' . $filename . '_whatizitUkPmcAll.rdf'
        );

        //Do relative paths
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