<?php

namespace App\Controller;

class BooksController extends AppController
{

    public $name = 'Books';

    public function index()
    {

        $this->Book->recursive = 0;
        $this->set('books', $this->paginate());
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid book'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('book', $this->Book->read(null, $id));
    }

    public function add()
    {

        if (!empty($this->request->data)) {
            $this->Book->create();
            if ($this->Book->save($this->request->data)) {
                $this->Session->setFlash(__('The book has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The book could not be saved. Please, try again.'));
            }
        }
        $courses = $this->Book->Course->find('list');
        $this->set(compact('courses'));
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(__('Invalid book'));
            return $this->redirect(array('action' => 'index'));
        }
        if (!empty($this->request->data)) {
            if ($this->Book->save($this->request->data)) {
                $this->Session->setFlash(__('The book has been saved'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The book could not be saved. Please, try again.'));
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->Book->read(null, $id);
        }
        $courses = $this->Book->Course->find('list');
        $this->set(compact('courses'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(__('Invalid id for book'));
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->Book->delete($id)) {
            $this->Session->setFlash(__('Book deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Book was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }
}
