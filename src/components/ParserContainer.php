<?php
    namespace Components;
    
    use Interfaces\Parser;
    
    class ParserContainer implements Parser
    {
        private $parser;
        
        public function setParser(Parser $parser) 
        {
            $this->parser = $parser;
        }

        public function parse() 
        {
            $this->parser->parse();
        }
        
        public function getData() 
        {
            return $this->parser->getData();
        }
    }