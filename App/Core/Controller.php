<?php
namespace App\Core;
use \App\Core\Mvc;
use App\Core\Validator;
use App\Core\Storage;

/**
 *  sommario di Controller
 * 
 * Tramite questa classe diamo la  base per 
 * i controllers che estenderanno questa classe
 * 
 * 
 */

class Controller
{

    public function __construct(public Mvc $mvc)
    {
       
    }

 

    /**
     * reindirizzamento alla cartella
     * @var $view inserire il file con estensione php per
     * visualizzare la pagina
     * @var array $message  
     * All'interno di questo array insieramo  tutti i valori che sostituiranno 
     * i placceholders. esempio {{page}} verrà sostituiro da una variabile con indice page presente in un array
     *  
     * per maggiori particolari,andare nel file View presente su /App/Core/View
     */
    public function render(string $view, array $variables = [], array|null $message = ['message' => ''])
    {

        $content = $this->mvc->view->render(page: $view, variables: $variables, message: $message);
        $this->mvc->response->setContent($content);
    }

    public function redirect(string $var)
    {
        $this->mvc->response->redirect($var);
    }

    public function statusCode413()
    {
        $this->mvc->response->set413();
    }

    public function redirectBack()
    {
        $back = $this->mvc->request->redirectBack();
        $this->mvc->response->redirect($back);
    }

    /**
     * @deprecated non esiste più
     * @param mixed $view
     * @param mixed $variables
     */
    public function view($view, $variables)
    {

        return $this->mvc->view->view($view, $variables);

    }

    private function sessionStorage(){
        return $this->mvc->sessionStorage;
    }
    public function withError($message)
    {
       $this->sessionStorage()->setFlashSession('error', $message);
    }

    public function withSuccess($message)
    {
        $this->sessionStorage()
        ->setFlashSession('success', $message);
    }

    public function withWarning($message)
    {
        $this->sessionStorage()->setFlashSession('warning', $message);
    }
  

    public static function validateImage($file)
    {
        // Controlla se il file è stato caricato senza errori
        if (isset($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
            // Verifica se il file è un'immagine
            $imageSize = @getimagesize($file['tmp_name']);
            return is_array($imageSize);
        }
        return false;
    }



    /**
     * Modifica il Layout della pagina
     */

    protected function setLayout(string $layout)
    {
        if (str_contains($layout, '.php')) {
            $layout = str_replace('.php', '', $layout);
        }

        $this->mvc->view->layout = $layout;
    }


}