<?php
namespace Cupon\OfertaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function ayudaAction()
    {
        return $this->render('OfertaBundle:Default:ayuda.html.twig');
    }
    
    public function portadaAction($ciudad)
    {
        /*if(null == $ciudad)
        {
            $ciudad = $this->container->getParameter('cupon.ciudad_por_defecto');
            return new RedirectResponse(
                $this->generateUrl('portada', array('ciudad' => $ciudad))
            );
        }*/
        
        $em = $this->getDoctrine()->getManager();
        
        $oferta = $em->getRepository('OfertaBundle:Oferta')->findOfertaDelDia($ciudad);
        /*$oferta = $em->getRepository('OfertaBundle:Oferta')->findOneBy(array(
            'ciudad' => $ciudad,
            'fechaPublicacion' => new \DateTime('today')
        ));*/
        
        if(!$oferta){
            throw $this->createNotFoundException(
                'No se ha encontrado la oferta del día en la ciudad seleccionada'
            );
        }
        
        return $this->render(
            'OfertaBundle:Default:portada.html.twig',
            array('oferta' => $oferta)
        );
    }
    
    public function ofertaAction($ciudad, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $oferta = $em->getRepository('OfertaBundle:Oferta')
                     ->findOferta($ciudad, $slug);
                     
        if(!$oferta){
            throw $this->createNotFoundException('No existe la oferta');
        }
        
        $relacionadas = $em->getRepository('OfertaBundle:Oferta')
                           ->findRelacionadas($ciudad);
                     
        return $this->render('OfertaBundle:Default:detalle.html.twig', array(
                'oferta' => $oferta,
                'relacionadas' => $relacionadas
            )
        );
    }
}
