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
        
        public function extractImage(\PhpOffice\PhpSpreadsheet\Worksheet\Drawing $image) 
        {
            static $i = 0;
            
            $zipReader = fopen($image->getPath(), 'r');
            $imageContents = '';
            while (!feof($zipReader)) {
                $imageContents .= fread($zipReader,1024);
            }
            fclose($zipReader);
            $extension = $image->getExtension();
            $myFileName = $this->file_name . '_' . $image->getCoordinates() . '_' . ($i++) . '.' . $extension;
            file_put_contents($myFileName, $imageContents);
        }

        public function parse()
        {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->file_name);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Save all images from excel file
            foreach ($worksheet->getDrawingCollection() as $image) 
            {
                if ($image instanceof \PhpOffice\PhpSpreadsheet\Worksheet\Drawing) {
                    $this->extractImage($image);
                }
            }
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

