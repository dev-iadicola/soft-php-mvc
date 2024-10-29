<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Mvc;
use App\Model\Portfolio;

class PortfolioManagerController extends Controller 
{

  public function __construct(public Mvc $mvc)
  {
    parent::__construct($mvc);

    $this->setLayout('admin');
  }

  public function index()
  {

    $portfolio = Portfolio::orderBy('id DESC')->get();
    $this->render('admin.portfolio.portfolio', [], compact('portfolio'));
  }

  public function store(Request $request)
  {   
    Portfolio::dirtySave($request->getPost());

    $this->redirectBack()->withSuccess('Portfolio Aggiornato con successo!');
  }

  public function edit(Request $request, $id)
  {
    $pfolio = Portfolio::find($id);
    $portfolio = Portfolio::orderBy('id DESC')->get();
    $this->render('admin.portfolio.portfolio', [], compact('portfolio', 'pfolio'));
  }

  public function update(Request $request, $id)
  {
    $data = $request->getPost();

    $project = Portfolio::find($id);
    $project->dirtyUpdate($data);

    $this->withSuccess('Aggiornamento Eseguito');
    $this->redirectBack();
  }

  public function destroy(Request $reqq, $id){
    // trova e azione
   $data =  $reqq->getPost();
   if( !isset($data['_method']) ||!$data['_method'] === 'DELETE'){
    return $this->statusCode413();
   }

    $project  = Portfolio::find($id);

    $project->delete();
// Feedback Server
    return $this->redirectBack()->withSuccess('Portfolio ELIMINATO');

 }

}
