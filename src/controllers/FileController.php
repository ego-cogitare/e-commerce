<?php
    namespace Controllers;

    class FileController
    {
        private $settings;
        
        public function __construct($rdb) {
            $this->settings = $rdb->get('settings');
        }
        
        public function __invoke($request, $response, $args)
        {
            // Get upload path
            $path = $request->getParam('path');
            
            // Server store directory
            $directory = '/' 
                . trim($this->settings['files']['upload']['directory'] 
                . '/' . trim($path, '/'), '/');
            
            $uploadedFiles = $request->getUploadedFiles();
            $uploadedFile = $uploadedFiles['uploadFile'];
            
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) 
            {
                $filename = $this->moveUploadedFile($directory, $uploadedFile);
                
                return $response->write(
                    json_encode([
                        'name' => $filename,
                        'path' => $path
                    ])
                );
            }
        }
        
        private function moveUploadedFile($directory, \Slim\Http\UploadedFile $uploadedFile)
        {
            if (!empty($this->settings['files']['upload']['keepNames']))
            {
                $filename = $uploadedFile->getClientFilename();
            }
            else 
            {
                $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
                $basename = bin2hex(random_bytes(8));
                $filename = sprintf('%s.%0.8s', $basename, $extension);
            }

            $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

            return $filename;
        }
    }