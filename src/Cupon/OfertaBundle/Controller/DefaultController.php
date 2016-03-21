<?php
namespace Cupon\OfertaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function ayudaAction()
    {
        return $this->render('OfertaBundle:Default:ayuda.html.twig');
    }
    
    public function portadaAction($ciudad = null)
    {
        if(null == $ciudad)
        {
            $ciudad = $this->container->getParameter('cupon.ciudad_por_defecto');
            return new RedirectResponse(
                $this->generateUrl('portada', array('ciudad' => $ciudad))
            );
        }
        
        $em = $this->getDoctrine()->getManager();
        
        $oferta = $em->getRepository('OfertaBundle:Oferta')->findOneBy(array(
            'ciudad' => $ciudad,
            'fechaPublicacion' => new \DateTime('today')
        ));
        
        return $this->render(
            'OfertaBundle:Default:portada.html.twig',
            array('oferta' => $oferta)
        );
    }
}
