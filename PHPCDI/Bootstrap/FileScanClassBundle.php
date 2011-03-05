<?php

namespace PHPCDI\Bootstrap;

/**
 * This class bundle add classes automatically by scanning the filesystem.
 */
class FileScanClassBundle implements \PHPCDI\API\Bootstrap\ClassBundle {

    private $bundles;
    private $classes;
    private $id;

    /**
     * Example structure:
     * /path/to/myclassRoot/
     *    |-MyNs/
     *        |-MySubNs/
     *           |-MyClass.php
     *    |-MyOtherNs
     *        |-MyOtherClass.php
     * $classRoot will be: /path/to/myclassRoot
     * If $namepase is MyNs MyClass.php will be found (and not MyOtherClass.php)
     *
     * @param string $id unique id of this class bundle
     * @param string $classRoot path to the folder with classes
     * @param string $namespace namespace of the classes (may contain \)
     */
    public function __construct($id, $classRoot, $rootNamespace) {
        $this->bundles = array();
        $this->classes = array();
        $this->id = $id;
        $this->scan($classRoot, $rootNamespace);
    }

    protected function scan($classRoot, $rootNamespace) {
        $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($classRoot), \RecursiveIteratorIterator::SELF_FIRST);

        foreach($it as $file) {
            if(!$it->isDot() && $file->isFile() && $file->isReadable() && substr($file->getFilename(), -4) == '.php') {
                $folder = str_replace('./', '', $file->getPath());
                $namespace = str_replace('/', '\\', $folder) . '\\';
                $className = substr($file->getBasename(), 0,  strrpos($file->getBasename(), '.php'));

                if(\strpos($namespace, $rootNamespace) === 0) {
                    $this->classes[] = $namespace.$className;
                }
            }
        }
    }

    public function addClassBundle(\PHPCDI\API\Bootstrap\ClassBundle $otherBundle) {
        $this->bundles[] = $otherBundle;
    }

    public function getClassBundles() {
        return $this->bundles;
    }

    public function getClasses() {
        return $this->classes;
    }

    public function getId() {
        return $this->id;
    }
}
