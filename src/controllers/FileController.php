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
                // Create file record
                $media = $this->addMediaRecord([
                    'name' => $this->moveUploadedFile($directory, $uploadedFile),
                    'path' => $path,
                    'size' => $uploadedFile->getSize(),
                    'type' => $uploadedFile->getClientMediaType(),
                ]);

                return $response->write(
                    json_encode($media->toArray())
                );
            }
        }

        private function addMediaRecord(array $file)
        {
            $media = new \Models\Media();
            $media->name = $file['name'];
            $media->path = $file['path'];
            $media->type = $file['type'];
            $media->size = $file['size'];
            $media->isDeleted = false;
            $media->save();

            return $media;
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
