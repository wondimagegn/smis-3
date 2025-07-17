<?php
declare(strict_types=1);
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;


class AttachmentsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('attachments');

        if (class_exists('Josegonzalez\Upload\Model\Behavior\UploadBehavior')) {
            debug('UploadBehavior class exists');
        } else {
            debug('UploadBehavior class NOT found');
        }

        $this->setDisplayField('file');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');



        $this->addBehavior('JoseGonzalez\Upload\Model\Behavior\UploadBehavior', [
            'file' => [
                'path' => 'webroot{DS}uploads{DS}attachments{DS}Student{DS}{field-value:student_id}{DS}{field-value:size}{DS}{field-value:group}{DS}',
                'fields' => [
                    'dir' => 'file_dir',
                    'size' => 'file_size',
                    'type' => 'file_type',
                ],
                'nameCallback' => function ($data, $settings) {
                    return strtolower(time() . '_' . $data['name']);
                },
                'transformer' => function ($data, $field, $settings, $entity) {
                    // Compute checksum
                    if (file_exists($data['tmp_name'])) {
                        $data['checksum'] = md5_file($data['tmp_name']);
                    }
                    // Set group based on MIME type
                    $data['group'] = strpos($data['type'], 'image') === 0 ? 'img' : 'doc';
                    // Set size
                    $data['size'] = 'original';

                    // Generate resized images for 'img' group
                    if ($data['group'] === 'img') {
                        $imagine = new Imagine();
                        $image = $imagine->open($data['tmp_name']);
                        $sizes = [
                            'l' => new Box(800, 800),
                            'm' => new Box(400, 400),
                            's' => new Box(100, 100),
                        ];
                        foreach ($sizes as $size => $box) {
                            $thumbnail = $image->thumbnail($box);
                            $thumbDir = WWW_ROOT . "uploads/attachments/Student/{$entity->student_id}/{$size}/{$data['group']}/";
                            if (!is_dir($thumbDir)) {
                                mkdir($thumbDir, 0755, true);
                            }
                            $thumbName = strtolower(time() . '_' . $data['name']);
                            $thumbPath = $thumbDir . $thumbName;
                            $thumbnail->save($thumbPath);
                            // Return thumbnail data for separate attachment
                            $thumbData = [
                                'model' => 'Student',
                                'foreign_key' => $entity->student_id,
                                'file' => $thumbName,
                                'file_dir' => "Uploads/attachments/Student/{$entity->student_id}/{$size}/{$data['group']}/",
                                'file_size' => filesize($thumbPath),
                                'file_type' => $data['type'],
                                'checksum' => md5_file($thumbPath),
                                'group' => $data['group'],
                                'size' => $size,
                                'student_id' => $entity->student_id,
                            ];
                            $thumbAttachment = $this->newEntity($thumbData);
                            $this->save($thumbAttachment);
                        }
                    }
                    return $data;
                },
                'allowedTypes' => ['image/jpeg', 'image/png', 'application/pdf', 'application/msword'],
                'allowedExtensions' => ['jpg', 'png', 'pdf', 'doc', 'docx'],
                'maxSize' => 2097152, // 2MB
            ],
        ]);
    }

    public function beforeSave($event, $entity, $options): void
    {
        if (isset($entity->file['checksum'])) {
            $entity->checksum = $entity->file['checksum'];
        }
        if (isset($entity->file['group'])) {
            $entity->group = $entity->file['group'];
        }
        if (isset($entity->file['size'])) {
            $entity->size = $entity->file['size'];
        }
        if (isset($entity->file['file_size'])) {
            $entity->file_size = $entity->file['file_size'];
        }
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create')

            ->requirePresence('model', 'create')
            ->notEmptyString('model')
            ->add('model', 'validModel', [
                'rule' => function ($value) {
                    return $value === 'Student';
                },
                'message' => 'Model must be Student.'
            ])

            ->requirePresence('foreign_key', 'create')
            ->notEmptyString('foreign_key')
            ->integer('foreign_key')
            ->min('foreign_key', 1, 'Foreign key must be a positive integer.')

            ->requirePresence('student_id', 'create')
            ->notEmptyString('student_id')
            ->integer('student_id')
            ->min('student_id', 1, 'Student ID must be a positive integer.')

            ->requirePresence('file', 'create')
            ->notEmptyFile('file', 'A file must be uploaded.')
            ->add('file', 'validUpload', [
                'rule' => function ($value, $context) {
                    if (is_array($value) && isset($value['error'])) {
                        return $value['error'] === UPLOAD_ERR_OK;
                    }
                    return true;
                },
                'message' => 'Invalid file upload.'
            ])

            ->requirePresence('file_dir', 'create')
            ->notEmptyString('file_dir')
            ->add('file_dir', 'validPath', [
                'rule' => function ($value, $context) {
                    return strpos($value, 'Uploads/attachments/') === 0;
                },
                'message' => 'Invalid file directory path.'
            ])

            ->allowEmptyString('file_size')
            ->integer('file_size')
            ->min('file_size', 0, 'File size must be non-negative.')

            ->allowEmptyString('file_type')
            ->add('file_type', 'validMime', [
                'rule' => function ($value, $context) {
                    $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword'];
                    return empty($value) || in_array($value, $allowedMimes);
                },
                'message' => 'Invalid MIME type.'
            ])

            ->allowEmptyString('checksum')
            ->add('checksum', 'validChecksum', [
                'rule' => function ($value, $context) {
                    return empty($value) || preg_match('/^[a-f0-9]{32}$/i', $value);
                },
                'message' => 'Invalid checksum format.'
            ])

            ->allowEmptyString('group')
            ->add('group', 'validGroup', [
                'rule' => function ($value, $context) {
                    $allowedGroups = ['img', 'doc'];
                    return empty($value) || in_array($value, $allowedGroups);
                },
                'message' => 'Invalid group.'
            ])

            ->allowEmptyString('size')
            ->add('size', 'validSize', [
                'rule' => function ($value, $context) {
                    $allowedSizes = ['original', 'l', 'm', 's'];
                    return empty($value) || in_array($value, $allowedSizes);
                },
                'message' => 'Invalid size.'
            ])

            ->allowEmptyString('alternative')
            ->maxLength('alternative', 255, 'Alternative name is too long.');

        return $validator;
    }

    public function emptyTable()
    {
        $table = $this->tablePrefix . $this->table;
        $result = $this->query("TRUNCATE $table");
        return $result;
    }
}
