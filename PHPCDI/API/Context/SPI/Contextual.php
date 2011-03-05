<?php

namespace PHPCDI\API\Context\SPI;

/**
 * 
 */
interface Contextual {
    /**
     * @param PHPCDI_API_Context_SPI_CreationalContext $creationalContext
     */
    public function create($creationalContext);

    /**
     * @param PHPCDI_API_Context_SPI_CreationalContext $creationalContext
     */
    public function destroy($instance, $creationalContext);
}

