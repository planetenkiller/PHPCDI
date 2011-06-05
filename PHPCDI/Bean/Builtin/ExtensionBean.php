<?php

namespace PHPCDI\Bean\Builtin;

use PHPCDI\SPI\Bean;
use PHPCDI\Manager\BeanManager;
use PHPCDI\SPI\Context\CreationalContext;
use PHPCDI\API\Annotations;
use PHPCDI\Util\ReflectionUtil;

class ExtensionBean implements Bean, DynamicLookupUnsupported, BuiltinBean {
    
    /**
     * @var \PHPCDI\Manager\BeanManager
     */
    private $beanManager;
    
    private $obj;
    
    public function __construct(BeanManager $beanManager, $obj) {
        $this->beanManager = $beanManager;
        $this->obj = $obj;
    }

    public function create(CreationalContext $creationalContext) {
       return $this->obj;
    }

    public function destroy($instance, CreationalContext $creationalContext) {
    }

    public function getBeanClass() {
        return get_class($this->obj);
    }

    public function getInjectionPoints() {
        return array();
    }

    public function getName() {
        return null;
    }

    public function getQualifiers() {
        return array(Annotations\DefaultObj::newInstance(), Annotations\Any::newInstance());
    }

    public function getScope() {
        return Annotations\ApplicationScoped::className();
    }

    public function getStereotypes() {
        return array();
    }

    public function getTypes() {
        return ReflectionUtil::getClassNames($this->getBeanClass());
    }

    public function isAlternative() {
        return false;
    }

    public function isNullable() {
        return true;
    }
    
    public function __toString() {
        return "Extenstion Bean of class " . $this->getBeanClass();
    }
}
