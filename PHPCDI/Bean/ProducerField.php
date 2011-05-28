<?php

namespace PHPCDI\Bean;

use PHPCDI\API\Inject\SPI\AnnotatedField;
use PHPCDI\API\Inject\SPI\Bean;

class ProducerField extends AbstractProducer {

    /**
     * @var BeanManager
     */
    private $beanManager;

    public function __construct(Bean $declaringBean, AnnotatedField $field, BeanManager $beanManager) {
        parent::__construct($declaringBean, $field, array());
        $this->beanManager = $beanManager;
        $this->setProducer(new \PHPCDI\Injection\FieldProducer($this));
    }

    public function getBeanManager() {
        return $this->beanManager;
    }

    public function destroy($instance, $creationalContext) {
        $this->producer->dispose($instance);
    }
}
