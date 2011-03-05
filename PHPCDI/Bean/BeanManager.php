<?php

namespace PHPCDI\Bean;

/**
 * BeanManager interface implementation
 */
class BeanManager implements \PHPCDI\API\Inject\SPI\BeanManager {

    private $beans;
    private $beansIterator;
    private $contexts;
    private $resolver;
    private $accessibleManagers;

    public function __construct(&$contexts=array()) {
        $this->accessibleManagers = array();
        $this->beans = new \ArrayObject(array());
        $this->beansIterator = new \ArrayIterator($this->beans);
        $this->contexts =& $contexts;

        $beans = $this->beans;
        $manager = $this;
        $it = function() use ($manager, $beans) {
            $it = new \AppendIterator();
            $stack = array();
            foreach(BeanManager::buildBeansIterator($manager, $stack) as $sit) {
                $it->append($sit);
            }
            return $it;
        };
        $this->resolver = new \PHPCDI\Resolution\TypeSafeReslover(new \PHPCDI\Util\LazyIterator($it));
    }

    public function addAccessibleBeanManager(BeanManager $manager) {
        $this->accessibleManagers[] = $manager;
    }

    public function addBean(\PHPCDI\API\Inject\SPI\Bean $bean) {
        $this->beans[] = $bean;
    }

    public function addContext(\PHPCDI\API\Context\SPI\Context $context) {
        if(isset($this->contexts[$context->getScope()])) {
            $this->contexts[$context->getScope()][] = $context;
        } else {
            $this->contexts[$context->getScope()] = array($context);
        }
    }

    public function createAnnotatedType($class) {

    }

    public function createCreationalContext($contextual) {
        return new \PHPCDI\Context\CreationalContextImpl($contextual);
    }

    public function createInjectionTarget($annotatedType) {

    }

    public function getBeans($beanType, $qualifiers) {
        return $this->resolver->reslove($beanType, $qualifiers);
    }

    /**
     * @param string $scopeType
     *
     * @return \PHPCDI\API\Context\SPI\Context
     */
    public function getContext($scopeType) {
        $activeContext = null;
        if(isset($this->contexts[$scopeType])) {
            foreach($this->contexts[$scopeType] as $context) {
                if($context->isActive()) {
                    if($activeContext == null) {
                        $activeContext = $context;
                    } else {
                        throw new \LogicException('More than one context active');
                    }
                }
            }
        }

        if($activeContext == null) {
            throw new \InvalidArgumentException('no active context found');
        }

        return $activeContext;
    }

    public function &getContexts() {
        return $this->contexts;
    }

    public function getInjectableReference($ij, $ctx) {
        $bean = $this->resolve($this->getBeans($ij->getType(), $ij->getQualifiers()));
        return $this->getRefernce($bean, $ij->getType(), $ctx);
    }

    public function getRefernce(\PHPCDI\API\Inject\SPI\Bean $bean, $beanType, \PHPCDI\API\Context\SPI\CreationalContext $ctx) {
        if($ctx instanceof \PHPCDI\Context\CreationalContextImpl) {
            $ctx = $ctx->getCreationalContext($bean);
        }
        return $this->getContext($bean->getScope())->get($bean, $ctx);
    }

    public function getStereotypeDefinition($stereotypeAnnotation) {

    }

    public function isNormalScope($annotationType) {

    }

    public function isQualifier($annotationType) {

    }

    public function isScope($annotationType) {

    }

    public function isStereotype($annotationType) {

    }

    public function resolve($beans) {
        if(count($beans) == 1) {
            return $beans[0];
        } else if(count($beans) > 1) {
            throw new \RuntimeException("ambiguous beans for dependency: ".\print_r($beans, true));
        } else {
            return null;
        }
    }

    public function validate($injectionPoint) {

    }

    public static function buildBeansIterator(BeanManager $manager, array &$stack) {
        $stack[] = $manager;
        $beans = array();
        $beans[\spl_object_hash($manager->beansIterator)] = $manager->beansIterator;
        foreach($manager->accessibleManagers as $accessibleManager) {
            // break circular references
            if(!\in_array($accessibleManager, $stack)) {
                $beans = \array_merge($beans, self::buildBeansIterator($accessibleManager, $stack));
            }
        }

        return $beans;
    }
}
