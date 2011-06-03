<?php

namespace PHPCDI\Bean\Builtin;

use PHPCDI\API\Inject\SPI\Bean;

class ExtensionBean implements Bean, DynamicLookupUnsupported, BuiltinBean {
    
    /**
     * @var \PHPCDI\Bean\BeanManager 
     */
    private $beanManager;
    
    private $obj;
    
    public function __construct(\PHPCDI\Bean\BeanManager $beanManager, $obj) {
        $this->beanManager = $beanManager;
        $this->obj = $obj;
    }

    public function create($creationalContext) {
       return $this->obj;
    }

    public function destroy($instance, $creationalContext) {
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
        return array(\PHPCDI\API\Inject\DefaultObj::newInstance(), \PHPCDI\API\Inject\Any::newInstance());
    }

    public function getScope() {
        return \PHPCDI\API\Inject\ApplicationScoped::className();
    }

    public function getStereotypes() {
        return array();
    }

    public function getTypes() {
        return \PHPCDI\Util\ReflectionUtil::getClassNames($this->getBeanClass());
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
