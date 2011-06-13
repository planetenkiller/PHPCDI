<?php

namespace PHPCDI\Util;

use Doctrine\Common\Annotations\Lexer;
use PHPCDI\API\Annotations as AnnotationsPkg;

/**
 * An extenstion to the default doctrine common annotation parser to support
 * phpdoc @param and @return annotations.
 */
class AnnotationParser extends \Doctrine\Common\Annotations\Parser {
    public function Annotation() {
        if($this->isNestedAnnotation) {
            return parent::Annotation();
        } else {
            $name = $this->getLexer()->glimpse();

            if($name != null && $name['type'] == Lexer::T_IDENTIFIER) {
                if($name['value'] == 'return' || $name['value'] == 'param' || $name['value'] == 'var' || $name['value'] == 'Annos') {
                    $this->match(Lexer::T_AT);
                    $this->match(Lexer::T_IDENTIFIER);
                    $anno = $this->getLexer()->token['value'];

                    if($name['value'] == 'Annos') {
                        $this->match(Lexer::T_OPEN_PARENTHESIS);
                        $this->isNestedAnnotation = true;
                        $annos = array();
                        $annos[] = parent::Annotation();

                        while($this->getLexer()->isNextToken(Lexer::T_AT)) {
                            $annos[] = parent::Annotation();
                        }

                        $this->match(Lexer::T_NONE);
                        if($this->getLexer()->token['value'] != '$') {
                            throw AnnotationException::syntaxError("Expected $, got '{$this->getLexer()->token['value']}' at position {$this->getLexer()->token['position']}");
                        }

                        $this->match(Lexer::T_IDENTIFIER);
                        $paramName = $this->getLexer()->token['value'];
                        $this->match(Lexer::T_CLOSE_PARENTHESIS);

                        $this->isNestedAnnotation = false;

                        return $this->newAnnotation(AnnotationsPkg\P::className(), array('value' => $annos, 'name' => $paramName));
                    } else {
                        $type = 'mixed';
                        
                        if($this->getLexer()->lookahead['type'] == Lexer::T_IDENTIFIER) {
                            $this->match(Lexer::T_IDENTIFIER);
                            $typeParts[] = $this->getLexer()->token['value'];
                            while ($this->getLexer()->isNextToken(Lexer::T_NAMESPACE_SEPARATOR)) {
                                $this->match(Lexer::T_NAMESPACE_SEPARATOR);
                                $this->match(Lexer::T_IDENTIFIER);
                                $typeParts[] = $this->getLexer()->token['value'];
                            }

                            $type = implode('\\', $typeParts);
                        }

                        if($name['value'] == 'param') {
                            $this->match(Lexer::T_NONE);
                            if($this->getLexer()->token['value'] != '$') {
                                throw AnnotationException::syntaxError("Expected $, got '{$this->getLexer()->token['value']}' at position {$this->getLexer()->token['position']}");
                            }

                            $this->match(Lexer::T_IDENTIFIER);
                            $paramName = $this->getLexer()->token['value'];

                            return $this->newAnnotation('PHPCDI\Util\PhpDoc\PhpDocParam', array('type' => $type, 'name' => $paramName));
                        } else if($name['value'] == 'return') {
                            return $this->newAnnotation('PHPCDI\Util\PhpDoc\PhpDocReturn', array('type' => $type));
                        } else {
                            return $this->newAnnotation('PHPCDI\Util\PhpDoc\PhpDocVar', array('type' => $type));
                        }
                    }
                }
            } 

            return parent::Annotation();
        }
    }
}
