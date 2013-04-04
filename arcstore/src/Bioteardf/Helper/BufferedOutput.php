<?php

namespace Bioteardf\Helper;

use Symfony\Component\Console\Output\Output;

/**
 * Buffered Output for the Console
 */
class BufferedOutput extends Output
{
    /**
     * @var array $buffer
     */ 
    private $buffer = array();

    // --------------------------------------------------------------

    /**
     * Writes a message to the output.
     *
     * @param string  $message A message to write to the output
     * @param Boolean $newline Whether to add a newline or not
     */
    protected function doWrite($message, $newline)
    {
        $this->buffer[] = array($message, $newline);
    }

    // --------------------------------------------------------------

    /**
     * Flush the buffer
     *
     * @return array
     */
    public function flush()
    {
        $buf = $this->buffer;
        $this->buffer = array();
        return $buf;
    }
}

/* EOF: BufferedOutput.php */