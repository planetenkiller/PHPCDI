<?php

namespace PHPCDI\Util;

use Doctrine\Common\Annotations\Lexer;

/**
 * An extenstion to the default doctrine common annotation parser to support
 * phpdoc @param and @return annotations.
 */
class AnnotationParser extends \Doctrine\Common\Annotations\Parser {
    public function Annotations()
    {
        $this->isNestedAnnotation = false;

        $annotations = array();
        $annot = $this->Annotation();

        if ($annot !== false) {
            $annotations[get_class($annot)] = $annot;
            $this->getLexer()->skipUntil(Lexer::T_AT);
        }

        while ($this->getLexer()->lookahead !== null && $this->getLexer()->isNextToken(Lexer::T_AT)) {
            $this->isNestedAnnotation = false;
            $annot = $this->Annotation();

            if ($annot !== false) {
                // ---- change -- multiple annotation support ----
                if(isset($annotations[get_class($annot)])) {
                    if(\is_array($annotations[get_class($annot)])) {
                        $annotations[get_class($annot)][] = $annot;
                    } else {
                        $annotations[get_class($annot)] = array($annotations[get_class($annot)], $annot);
                    }
                } else {
                    $annotations[get_class($annot)] = $annot;
                }
                // ---- change end --------
                $this->getLexer()->skipUntil(Lexer::T_AT);
            }
        }

        return $annotations;
    }

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

                        return $this->newAnnotation('PHPCDI\API\Inject\P', array('value' => $annos, 'name' => $paramName));
                    } else {
                        $this->match(Lexer::T_IDENTIFIER);
                        $typeParts[] = $this->getLexer()->token['value'];
                        while ($this->getLexer()->isNextToken(Lexer::T_NAMESPACE_SEPARATOR)) {
                            $this->match(Lexer::T_NAMESPACE_SEPARATOR);
                            $this->match(Lexer::T_IDENTIFIER);
                            $typeParts[] = $this->getLexer()->token['value'];
                        }

                        $type = implode('\\', $typeParts);

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
