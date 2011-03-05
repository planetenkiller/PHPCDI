<?php

namespace PHPCDI\Example\Simple;


class Config {
    /**
     * @Produces
     * @ApplicationScoped
     * @return PHPCDI\Example\Simple\Logger
     */
    public function logger() {
        return new StreamLogger('php://output');
    }
}

/**
 * Logger Implementation (not in its own file because its private)
 */
class StreamLogger implements Logger {
    private $output;

    public function __construct($output) {
        $this->output = \fopen($output, 'a+');
    }

    public function log($msg) {
        \fputs($this->output, \date('c') . ' - PRIVATE - ' . $msg);
    }
}

