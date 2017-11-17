<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Teams;
use Symfony\Component\HttpFoundation\Request;

/**
*@Route("/players")
*/
class PlayersController extends Controller
{
    /**
     * @Route("/index", name="players_index")
     */
    public function indexAction()
    {
        return $this->render('AppBundle:Players:index.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/add", name="players_add")
     */
    public function addAction(Request $request)
    {






        return $this->render('AppBundle:Players:add.html.twig', array(
        
        ));
    }

    /**
     * @Route("/delete", name="players_delete")
     */
    public function deleteAction()
    {
        return $this->render('AppBundle:Players:delete.html.twig', array(
            // ...
        ));
    }

    /**
     * @Route("/edit", name="players_edit")
     */
    public function editAction()
    {
        return $this->render('AppBundle:Players:edit.html.twig', array(
            // ...
        ));
    }

}
