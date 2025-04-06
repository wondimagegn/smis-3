<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;

class BackupsController extends AppController
{

    public $name = 'Backups';

    public $menuOptions = array(
        'exclude' => array('deleteOneMonthOldBackup'),
        'alias' => array(
            'index' => 'Download Backup',
        )
    );

    public $components = array('DataTable');
    public $paginate = array();

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->Allow('deleteOneMonthOldBackup');
    }

    public function __init_search()
    {

        if (!empty($this->request->data['Backup'])) {
            $search_session = $this->request->data['Backup'];
            $this->Session->write('backup_search_data', $search_session);
        } else {
            $search_session = $this->Session->read('backup_search_data');
            $this->request->data['Backup'] = $search_session;
        }
    }

    public function takeBackup($cron = false)
    {

        //read the command from smis.php
        if (isset($this->request->data['generateDatabaseBackup'])) {
            $command = Configure::read('Utility.command');
            debug($command);
            $output = shell_exec($command . " > cause.text");
            $this->Flash->success(
                __(
                    'You have successfully generated database backup, you can download the backup from the following list.'
                )
            );
            return $this->redirect(array('action' => 'index'));
        }
    }

    public function index($backup_id = null)
    {

        $this->__init_search();
        $this->paginate['limit'] = 20;
        $this->paginate['order'] = array('Backup.created' => 'DESC');
        $backupList = array();
        $files_for_download = array();
        $dir = new Folder(Configure::read('Utility.backupPath'));
        //Looking for major backup file types
        $files1 = $dir->find('.*\.zip');
        $files2 = $dir->find('.*\.tar');
        $files3 = $dir->find('.*\.gz');
        $files4 = $dir->find('.*\.sql');
        $files = array_merge($files1, $files2, $files3, $files4);

        if (!empty($backupList)) {
            foreach ($files as $file) {
                $index = count($files_for_download);
                $file = new File($dir->pwd() . DS . $file);
                $backupDetail = $this->Backup->find('first', array(
                    'conditions' => array(
                        'Backup.name' => $file->name,
                        'Backup.location' => $file->Folder->path,
                        'Backup.mime' => mime_content_type($file->path)
                    ),
                    'recursive' => -1
                ));

                if (empty($backupDetail) && $file->size() > 0) {
                    $bl_index = count($backupList);
                    $backupList[$bl_index]['Backup']['operation_type'] = 'Backup';
                    $backupList[$bl_index]['Backup']['location'] = $file->Folder->path;
                    $backupList[$bl_index]['Backup']['name'] = $file->name;
                    $backupList[$bl_index]['Backup']['size'] = $file->size();
                    $backupList[$bl_index]['Backup']['mime'] = mime_content_type($file->path);
                    $backupList[$bl_index]['Backup']['backup_taken'] = 0;
                    $backupList[$bl_index]['Backup']['created'] = date('Y-m-d H:i:s', $file->lastChange());
                }
            }
        }

        if (!empty($backupList)) {
            $this->Backup->saveAll($backupList);
        }

        if (isset($this->request->data['viewBackup']) && !empty($this->request->data['Backup'])) {
            $backup_from_date = $this->request->data['Backup']['backup_date_from'];
            $backup_to_date = $this->request->data['Backup']['backup_date_to'];
            $backup_from_date = $backup_from_date['year'] . '-' . $backup_from_date['month'] . '-' . $backup_from_date['day'] . ' 00:00:00';
            $backup_to_date = $backup_to_date['year'] . '-' . $backup_to_date['month'] . '-' . $backup_to_date['day'] . ' 23:59:59';
            $this->paginate['conditions']['Backup.created >='] = $backup_from_date;
            $this->paginate['conditions']['Backup.created <='] = $backup_to_date;
        }

        $this->Paginator->settings = $this->paginate;
        //debug($this->Paginator->settings);
        $files_for_download = $this->Paginator->paginate('Backup');

        if (!empty($files_for_download)) {
            foreach ($files_for_download as &$ffd_value) {
                if (file_exists($ffd_value['Backup']['location'] . DS . $ffd_value['Backup']['name'])) {
                    $ffd_value['Backup']['file_exists'] = true;
                } else {
                    $ffd_value['Backup']['file_exists'] = false;
                }
            }
        }

        if (empty($files_for_download)) {
            if (!empty($this->request->data)) {
                $this->Flash->info(__('There is no backup in the selected date range.'));
            } else {
                $this->Flash->info(__('There is no backup to download.'));
            }
        }

        $this->set(compact('files_for_download'));
        $response = $files_for_download;
        $this->set('_serialize', 'response');

        if (!empty($backup_id)) {
            $backupDetail = $this->Backup->find(
                'first',
                array('conditions' => array('Backup.id' => $backup_id), 'recursive' => -1)
            );

            if (!empty($backupDetail) && file_exists(
                    $backupDetail['Backup']['location'] . DS . $backupDetail['Backup']['name']
                )) {
                $backupDetail['Backup']['backup_taken'] = 1;
                if ($backupDetail['Backup']['first_backup_taken_date'] == 0 || $backupDetail['Backup']['first_backup_taken_date'] == null || $backupDetail['Backup']['first_backup_taken_date'] == '0000-00-00') {
                    $backupDetail['Backup']['first_backup_taken_date'] = date('Y-m-d H:i:s');
                }

                $backupDetail['Backup']['last_backup_taken_date'] = date('Y-m-d H:i:s');
                $this->Backup->save($backupDetail);
                $file_extension = strtolower(
                    substr($backupDetail['Backup']['name'], strripos($backupDetail['Backup']['name'], '.') + 1)
                );
                $this->viewClass = 'Media';
                $params = array(
                    'id' => $backupDetail['Backup']['name'],
                    'name' => substr(
                        $backupDetail['Backup']['name'],
                        0,
                        strripos($backupDetail['Backup']['name'], '.')
                    ),
                    'download' => true,
                    'extension' => $file_extension, // must be lower case
                    'mimeType' => array($file_extension => $backupDetail['Backup']['mime']),
                    'path' => Configure::read('Utility.backupPath') // don't forget terminal 'DS'
                );
                $this->set($params);
            }
        }
    }

    public function deleteOneMonthOldBackup($cron = false)
    {

        $backupList = array();
        //debug(Configure::read('Utility.command'));
        $dir = new Folder(Configure::read('Utility.backupPath'));

        $files1 = $dir->find('.*\.zip');
        $files2 = $dir->find('.*\.tar');
        $files3 = $dir->find('.*\.gz');
        $files4 = $dir->find('.*\.sql');
        $files = $files1 + $files2 + $files3 + $files4;

        if (!empty($files)) {
            foreach ($files as $file) {
                $file = new File($dir->pwd() . DS . $file);
                $backupDetail = $this->Backup->find('first', array(
                    'conditions' => array(
                        'Backup.name' => $file->name,
                        'Backup.location' => $file->Folder->path,
                        'Backup.created <=' => date("Y-m-d 23:59:59", strtotime("-32 day"))
                    ),
                    'recursive' => -1
                ));

                if (!empty($backupDetail)) {
                    $bl_index = count($backupList);
                    $backupList[$bl_index]['Backup']['operation_type'] = 'Backup';
                    $backupList[$bl_index]['Backup']['location'] = $file->Folder->path;
                    $backupList[$bl_index]['Backup']['name'] = $file->name;
                    $backupList[$bl_index]['Backup']['size'] = $file->size();
                    $backupList[$bl_index]['Backup']['mime'] = mime_content_type($file->path);
                    $backupList[$bl_index]['Backup']['backup_taken'] = 0;
                    $backupList[$bl_index]['Backup']['created'] = date('Y-m-d H:i:s', $file->lastChange());
                }
            }
        }

        $full_file_path = array();

        if (!empty($backupList)) {
            foreach ($backupList as $bcindex => $bv) {
                $full_file_path[] = $bv['Backup']['location'] . '/' . $bv['Backup']['name'];
            }
        }

        if ($cron) {
            return $full_file_path;
        }
    }
}
