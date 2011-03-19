<?php

namespace PHPCDI\Example\Decorators;

/**
 * @Decorator
 */
class DateLoggingDecorator implements Logger {
    
    /**
     * @Inject @Delegate
     * @var PHPCDI\Example\Decorators\Logger 
     */
    private $delegate;

    public function log($msg, $ex) {
        $this->delegate->log(\date('Y-m-d H:i') . ': ' . $msg, $ex);
    }
}

