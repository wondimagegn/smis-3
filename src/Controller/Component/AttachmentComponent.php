<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Utility\Text;
use Cake\Http\UploadedFile;

class AttachmentComponent extends Component
{
    public function processAttachments(array $files, string $model, string $group = 'general', string $targetDir = 'uploads/')
    {
        $data = [];
        $dir = WWW_ROOT . $targetDir;

        foreach ($files as $file) {
            if ($file instanceof UploadedFile && $file->getError() === UPLOAD_ERR_OK) {
                $filename = time() . '-' . Text::slug(pathinfo($file->getClientFilename(), PATHINFO_FILENAME)) . '.' . pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
                $file->moveTo($dir . $filename);

                $data[] = [
                    'model'        => $model,
                    'dirname'      => $targetDir,
                    'basename'     => $filename,
                    'filename'     => $targetDir . $filename,
                    'mime_type'    => $file->getClientMediaType(),
                    'size'         => $file->getSize(),
                    'checksum'     => md5_file($dir . $filename),
                    'group'        => $group,
                    'alternative'  => $filename
                ];
            }
        }

        return $data;
    }
}

