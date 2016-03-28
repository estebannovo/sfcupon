<?php

namespace Cupon\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use Cupon\UsuarioBundle\Entity\Usuario;
use Cupon\UsuarioBundle\Form\Frontend\UsuarioType;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('UsuarioBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function comprasAction()
    {
        $usuario_id = 1;
        
        $em = $this->getDoctrine()->getManager();
        $compras = $em->getRepository('UsuarioBundle:Usuario')
                      ->findTodasLasCompras($usuario_id);
        
        return $this->render('UsuarioBundle:Default:compras.html.twig', array(
            'compras' => $compras
        )); 
    }
    
    public function loginAction(Request $peticion)
    {
        $session = $peticion->getSession();
        
        // obtener, si existe, el error producido en el último intento de login
        if($peticion->attributes->has(SecurityContext::AUTHENTICATION_ERROR )){
            $error = $peticion->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        return $this->render('UsuarioBundle:Default:login.html.twig', array(
           'last_username' => $session->get(SecurityContext::LAST_USERNAME ),
           'error' => $error
        ));
    }
    
    public function cajaLoginAction(Request $peticion)
    {
        $session = $peticion->getSession();
        
        // obtener, si existe, el error producido en el último intento de login
        if($peticion->attributes->has(SecurityContext::AUTHENTICATION_ERROR )){
            $error = $peticion->attributes->get(
            SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        
        return $this->render('UsuarioBundle:Default:cajaLogin.html.twig', array(
           'last_username' => $session->get(SecurityContext::LAST_USERNAME ),
           'error' => $error
        ));
    }
    
    public function registroAction(Request $peticion)
    {
        $usuario = new Usuario();
        $usuario->setPermiteEmail(true);
        $usuario->setFechaNacimiento(new \DateTime('today - 18 years'));
        
        $formulario = $this->createForm(new UsuarioType(), $usuario);
        $formulario->add('registrarme', 'submit');
        
        $formulario->handleRequest($peticion);
        
        if($formulario->isValid()){
            $encoder = $this->get('security.encoder_factory')
                            ->getEncoder($usuario);
            
            $usuario->setSalt(md5(time()));
            $passwordCodificado = $encoder->encodePassword(
                    $usuario->getPassword(),
                    $usuario->getSalt()
            );
            
            $usuario->setPassword($passwordCodificado);
            
            $usuario->setFechaAlta(new \DateTime('now'));
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('info',
                '¡Enhorabuena! Te has registrado correctamente en Cupon'
            );
            
            $token = new UsernamePasswordToken(
                    $usuario,
                    $usuario->getPassword(),
                    'frontend',
                    $usuario->getRoles()
            );
            
            $this->container->get('security.context')->setToken($token);
            
            return $this->redirect($this->generateUrl('portada', array(
               'ciudad'  => $usuario->getCiudad()->getSlug()
            )));
        }
        
        return $this->render(
            'UsuarioBundle:Default:registro.html.twig',
            array('formulario' => $formulario->createView())
        );
    }
    
    public function perfilAction(Request $peticion)
    {
        $usuario = $this->get('security.context')->getToken()->getUser();
        $formulario = $this->createForm(new UsuarioType(), $usuario);        
        $formulario->add('guardar', 'submit', array('label' => 'Guardar cambios'));
        
        $passwordOriginal = $formulario->getData()->getPassword();
        
        $formulario->handleRequest($peticion);
        
        if ($formulario->isValid()) {
            if(null == $usuario->getPassword()){
                $usuario->setPassword($passwordOriginal);
            }else{
                $encoder = $this->get('security.encoder_factory')
                                ->getEncoder($usuario);
                $passwordCodificado = $encoder->encodePassword(
                    $usuario->getPassword(),
                        $usuario->getSalt()
                );
                $usuario->setPassword($passwordCodificado);
            }
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($usuario);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('info', 
                'Los datos de tu perfil se han actualizado correctamente'
            );
            
            return $this->redirect($this->generateUrl('usuario_perfil'));
        }
        
        return $this->render('UsuarioBundle:Default:perfil.html.twig', array(
            'usuario' => $usuario,
            'formulario' => $formulario->createView()
        ));
    }
}
