<?php
    namespace Controllers;

    class FileController
    {
        public function moveUploadedFile($directory, \Slim\Http\UploadedFile $uploadedFile)
        {
            if (true) 
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
        
        public function __invoke($request, $response, $args)
        {
            $directory = __DIR__ . '/../../public/uploads';
            $uploadedFiles = $request->getUploadedFiles();
            $uploadedFile = $uploadedFiles['uploadFile'];
            
            if ($uploadedFile->getError() === UPLOAD_ERR_OK) 
            {
                $filename = $this->moveUploadedFile($directory, $uploadedFile);
                
                return $response->write(
                    json_encode([
                        'file' => $filename
                    ])
                );
            }
        }
    }