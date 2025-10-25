<?php
namespace App\Core;
use \App\Core\Mvc;
use App\Core\Validator;
use App\Core\Storage;
use App\Core\Services\SessionService;

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
        $mvc->sessionService->verifyTimeFlashSession();
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

    public function view($view, $variables)
    {

        return $this->mvc->view->view($view, $variables);

    }

    public function withError($message)
    {
        SessionService::setFlashSession('error', $message);
    }

    public function withSuccess($message)
    {
        SessionService::setFlashSession('success', $message);
    }

    public function withWarning($message)
    {
        SessionService::setFlashSession('warning', $message);
    }

    public function resetImg(array $data)
    {

        if ($data['img']['error'] === UPLOAD_ERR_NO_FILE) {
            unset($data['img']);
        } elseif ($data['img']['error'] !== UPLOAD_ERR_NO_FILE) {
            (new Storage($this->mvc))->deleteFile($data['img']);
            $data['img'] = $this->checkImage($data);

        }
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

   



    public function checkPdf($data)
    {
        $validImage = Validator::validatePDF(
            $data['img']
        );
        if ($validImage === FALSE) {
            $this->withError('Non sono accettati formati che non sono immagini');
            return $this->redirectBack();
        }

        $uploadFile = new Storage($this->mvc);
        $uploadFile->storeFile($data['img']);

        return $uploadFile->getPathImg();
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