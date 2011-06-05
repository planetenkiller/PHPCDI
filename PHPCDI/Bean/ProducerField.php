<?php

namespace PHPCDI\Bean;

use PHPCDI\SPI\AnnotatedField;
use PHPCDI\SPI\Bean;
use PHPCDI\Manager\BeanManager;
use PHPCDI\Injection\FieldProducer;
use PHPCDI\SPI\Context\CreationalContext;

class ProducerField extends AbstractProducer {

    /**
     * @var \PHPCDI\Manager\BeanManager
     */
    private $beanManager;

    public function __construct(Bean $declaringBean, AnnotatedField $field, BeanManager $beanManager) {
        parent::__construct($declaringBean, $field, array());
        $this->beanManager = $beanManager;
        $this->setProducer(new FieldProducer($this));
    }

    public function getBeanManager() {
        return $this->beanManager;
    }

    public function destroy($instance, CreationalContext $creationalContext) {
        $this->producer->dispose($instance);
    }
}
