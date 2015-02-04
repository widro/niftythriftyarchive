<?php

namespace NiftyThrifty\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class ContentController extends Controller
{
    /**
     * The content controller serves static pages.  These render the
     * the templates described in the slug.  IE: about-us, contact-us, etc.
     */
    public function indexAction($slug)
    {
    	if(($slug=="home")||($slug=="registerbonus")){
			$collections = $this->getDoctrine()
								->getManager()
								->getRepository('NiftyThriftyShopBundle:Collection')
								->findAllActive(3);

			return $this->render('NiftyThriftyShopBundle:Content:home.html.twig',
									array('collections' => $collections, 'slug' => $slug));
    	}
    	elseif($slug=="contact-us"){
		    $request = $this->getRequest();

			$viewtype = 'showform';
			$errors = '';
            if ($request->getMethod() == 'POST'){

				$message = $_POST['message'];
				$first_name = $message['first_name'];
				$last_name = $message['last_name'];
				$sailthru_subject = $message['subject'];
				$nifty_toemail = $message['email'];
				$sailthru_body = $message['content'];

				if(!$sailthru_body){
					$errors .= "Message is required";
				} else{
					$viewtype = 'thanks';
					$mailer = new \Sailthru_Client($this->container->getParameter('sailthru_api_key'),
												   $this->container->getParameter('sailthru_api_secret'));

					$sendnow = $mailer->send('Transactions','help@niftythrifty.com' ,array('nifty_subject' => $sailthru_subject, 'nifty_body' => $sailthru_body, 'nifty_fromemail' => $nifty_toemail));
				}



            }
			return $this->render("NiftyThriftyShopBundle:Content:$slug.html.twig",
								array('viewtype' => $viewtype,
								'errors' => $errors));

    	} else{
			try {
				return $this->render("NiftyThriftyShopBundle:Content:$slug.html.twig");
			} catch (\InvalidArgumentException $e) {
				throw $this->createNotFoundException('Page not found.');
			}
        }
    }
}
