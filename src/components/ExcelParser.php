<?php
    namespace Components;
    
    use Interfaces\Parser;
    
    class ExcelParser implements Parser 
    {
        private $file_name;
        private $config;
        private $data = [];
        
        public function __construct(string $file_name, array $config) 
        {
            if (!file_exists($file_name))
            {
                throw new \Exception('File not found: ' . $file_name);
            }
            $this->file_name = $file_name;
            $this->config = $config;
        }
        
        public function __toString() 
        {
            printf('File name: %sParser config: %s', 
                $this->file_name . PHP_EOL,
                print_r($this->config, true)
            );
        }

        public function parse()
        {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->file_name);
            $worksheet = $spreadsheet->getActiveSheet();
            $this->data = $worksheet->toArray();
        }
        
        public function getData() 
        {
            return array_slice(
                $this->data, 
                $this->config['dataRows'][0], 
                $this->config['dataRows'][1] - $this->config['dataRows'][0]
            );
        }
    }

