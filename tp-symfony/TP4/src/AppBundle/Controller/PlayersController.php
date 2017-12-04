<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Teams;
use AppBundle\Entity\Players;
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
      $player = $this->getDoctrine()->getRepository(Players::class)->findAll();
      $team = $this->getDoctrine()->getRepository(Teams::class)->findAll();

        return $this->render('AppBundle:Players:index.html.twig', array(
            'players' => $player,
            'teams' => $team
        ));
    }

    /**
     * @Route("/add", name="players_add")
     */
    public function addAction(Request $request)
    {
      $player = new Players();
      $playerform = $this->createFormBuilder($player)
      ->add('firstname', TextType::class, array(
        'label' => 'Prénom :'
      ))
      ->add('lastname', TextType::class, array(
        'label' => 'Nom de famille :'
      ))
      ->add('age', IntegerType::class, array(
        'label' => 'Âge :'
      ))
      ->add('height', IntegerType::class, array(
        'label' => 'Taille (en cm) :'
      ))
      ->add('weight', IntegerType::class, array(
        'label' => 'Poids (en kg) :'
      ))
      ->add('team', EntityType::class, array(
        'class' => 'AppBundle:Teams',
        'choice_label' => 'name'
      ))
      ->add('picture', FileType::class, array(
        'label' => 'Photo du joueur'
      ))
      ->add('submit', SubmitType::class, array(
        'label' => 'Enregistrer',
        'attr' => array('class' => 'btn btn-primary btn-xs')
      ))
      ->getForm();

      $playerform->handleRequest($request);
      if($playerform->isSubmitted()) {
        $player = $playerform->getData();

        $pic = $player->getPicture();
        $picname = 'pic_' . strtolower($player->getFirstName()) . '_' . strtolower($player->getLastName()) . '.' . $pic->guessExtension();

        $pic->move($this->getParameter('dir_picture'), $picname);
        $player->setPicture($picname);

        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->flush();

      return $this->redirectToRoute('players_index');
      }



        return $this->render('AppBundle:Players:add.html.twig', array(
          'playerform' => $playerform->createView()
        ));
    }

    /**
     * @Route("/delete{id}", name="players_delete")
     */
    public function deleteAction($id)
    {
      $player = $this->getDoctrine()->getRepository(Players::class)->find($id);
      $em = $this->getDoctrine()->getManager();
      $em->remove($player);
      $em->flush();

        return $this->redirectToRoute('players_index');

    }

    /**
     * @Route("/edit{id}", name="players_edit")
     */
    public function editAction($id, Request $request)
    {
      $em = $this->getDoctrine()->getManager();
      $player = $em->getRepository(Players::class)->find($id);
      $playerformEdit = $this->createFormBuilder($player)
      ->add('firstname', TextType::class, array(
        'label' => 'Prénom :'
      ))
      ->add('lastname', TextType::class, array(
        'label' => 'Nom de famille :'
      ))
      ->add('age', IntegerType::class, array(
        'label' => 'Âge :'
      ))
      ->add('height', IntegerType::class, array(
        'label' => 'Taille (en cm) :'
      ))
      ->add('weight', IntegerType::class, array(
        'label' => 'Poids (en kg) :'
      ))
      ->add('team', EntityType::class, array(
        'class' => 'AppBundle:Teams',
        'choice_label' => 'name',
        'label' => 'Équipe :'
      ))
      ->add('picture', FileType::class, array(
        'label' => 'Photo du joueur',
        'data_class' => null
      ))
      ->add('submit', SubmitType::class, array(
        'label' => 'Mettre à jour',
        'attr' => array('class' => 'btn btn-primary btn-xs')
      ))
      ->getForm();

      $playerformEdit->handleRequest($request);
      if($request->getMethod() == 'POST') {
        $player = $playerformEdit->getData();

        $pic = $player->getPicture();
        $picname = 'pic_' . strtolower($player->getFirstName()) . '_' . $player->getLastName() . '.' . $pic->guessExtension();

        $pic->move($this->getParameter('dir_picture'), $picname);
        $player->setPicture($picname);

        $em = $this->getDoctrine()->getManager();
        $em->persist($player);
        $em->flush();

         return $this->redirectToRoute('players_index');
        }
        return $this->render('AppBundle:Players:edit.html.twig', array(
            'playerformEdit' => $playerformEdit->createView()
        ));
    }

}
