<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Datasource\Exception\RecordNotFoundException;

class BooksController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Flash');
        $this->loadComponent('Paginator');
    }

    public function index()
    {
        $booksTable = TableRegistry::getTableLocator()->get('Books');
        $this->set('books', $this->Paginator->paginate($booksTable->find()));
    }

    public function view($id = null)
    {
        if (!$id) {
            $this->Flash->error(__('Invalid book'));
            return $this->redirect(['action' => 'index']);
        }

        $booksTable = TableRegistry::getTableLocator()->get('Books');
        try {
            $book = $booksTable->get($id);
            $this->set('book', $book);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Invalid book'));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function add()
    {
        $booksTable = TableRegistry::getTableLocator()->get('Books');
        $book = $booksTable->newEntity();

        if ($this->request->is('post')) {
            $book = $booksTable->patchEntity($book, $this->request->getData());
            if ($booksTable->save($book)) {
                $this->Flash->success(__('The book has been saved'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The book could not be saved. Please, try again.'));
        }

        $coursesTable = TableRegistry::getTableLocator()->get('Courses');
        $courses = $coursesTable->find('list')->all()->toArray();
        $this->set(compact('book', 'courses'));
    }

    public function edit($id = null)
    {
        if (!$id) {
            $this->Flash->error(__('Invalid book'));
            return $this->redirect(['action' => 'index']);
        }

        $booksTable = TableRegistry::getTableLocator()->get('Books');
        try {
            $book = $booksTable->get($id);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Invalid book'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['post', 'put'])) {
            $book = $booksTable->patchEntity($book, $this->request->getData());
            if ($booksTable->save($book)) {
                $this->Flash->success(__('The book has been saved'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The book could not be saved. Please, try again.'));
        }

        $coursesTable = TableRegistry::getTableLocator()->get('Courses');
        $courses = $coursesTable->find('list')->all()->toArray();
        $this->set(compact('book', 'courses'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        if (!$id) {
            $this->Flash->error(__('Invalid id for book'));
            return $this->redirect(['action' => 'index']);
        }

        $booksTable = TableRegistry::getTableLocator()->get('Books');
        try {
            $book = $booksTable->get($id);
            if ($booksTable->delete($book)) {
                $this->Flash->success(__('Book deleted'));
            } else {
                $this->Flash->error(__('Book was not deleted'));
            }
        } catch (RecordNotFoundException $e) {
            $this->Flash->error(__('Invalid id for book'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
?>
