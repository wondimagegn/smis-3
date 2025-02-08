<?php
namespace App\Controller;

use App\Controller\AppController;


class BooksController extends AppController
{

    public function index()
    {
        $books = $this->paginate($this->Books);

        $this->set(compact('books'));
    }

    public function view($id = null)
    {
        $book = $this->Books->get($id, [
            'contain' => ['Courses'],
        ]);

        $this->set('book', $book);
    }

    public function add()
    {
        $book = $this->Books->newEntity();
        if ($this->request->is('post')) {
            $book = $this->Books->patchEntity($book, $this->request->getData());
            if ($this->Books->save($book)) {
                $this->Flash->success(__('The book has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The book could not be saved. Please, try again.'));
        }

        $this->set(compact('book'));
    }


    public function edit($id = null)
    {
        $book = $this->Books->get($id, [
            'contain' => ['Courses'],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $book = $this->Books->patchEntity($book, $this->request->getData());
            if ($this->Books->save($book)) {
                $this->Flash->success(__('The book has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The book could not be saved. Please, try again.'));
        }

        $this->set(compact('book', 'courses'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $book = $this->Books->get($id);
        if ($this->Books->delete($book)) {
            $this->Flash->success(__('The book has been deleted.'));
        } else {
            $this->Flash->error(__('The book could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
