<?php

namespace VerseOfDayBundle\Controller;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
//$version;
$session = new Session();
if($session->isStarted()){
$session->start();

$session->set('version', 'NLT');
//$version = "NIV";

}
//else
$version = $session->get('version');

$url = "https://www.biblegateway.com/votd/get/?format=json&version=".$version;

$opts = array(
	  'http'=>array(
	    'method'=>"GET",
	    'header'=>"request_modules: true\r\n"
	  )
	);
	$context = stream_context_create($opts);
		$response = file_get_contents($url,false,$context);
	$json = json_decode($response,true);
	$verse = html_entity_decode($json['votd']['content']);
	$reference = html_entity_decode($json['votd']['reference']);
	$versionid = $json['votd']['version_id'];
//	echo "Verse: ".html_entity_decode($json['votd']['content'])."\n";
//	echo "Reference: ".html_entity_decode($json['votd']['reference'])."\n";
        return $this->render('VerseOfDayBundle:Default:index.html.twig', array('verse'=>$verse, 'reference'=>$reference,'version'=>$versionid));
    }

	public function changeVersionAction(){
		$session = new Session();
		if(!$session->get('version'))
		{
                        $session->start();
			$session->set('version', 'NLT');
		}
		$version = $session->get('version');

	return $this->render('VerseOfDayBundle:Default:change.html.twig',array('version'=>$version));
	}

	public function changePOSTAction(){
		$request = Request::createFromGlobals();
		$session = new Session();
		if(!$session->get('version'))
			$session->start();
		$version = $request->request->get('version');
		$session->set('version', $version );
		return $this->indexAction();
	}
}
