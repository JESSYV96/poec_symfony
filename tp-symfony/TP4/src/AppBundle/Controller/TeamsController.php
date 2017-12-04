<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use AppBundle\Entity\Teams;
use Symfony\Component\HttpFoundation\Request;
/**
*@Route("/teams")
*/
class TeamsController extends Controller
{
    /**
     * @Route("/index", name="teams_index")
     */
    public function indexAction()
    {
      $teams = $this->getDoctrine()->getRepository(Teams::class)
          ->findAll();
        return $this->render('AppBundle:Teams:index.html.twig', array(
            'teams' => $teams
        ));
    }

    /**
     * @Route("/add", name="teams_add")
     */
    public function addAction(Request $request)
    {
      $team = new Teams();
      $teamform = $this->createFormBuilder($team)
      ->add('name', TextType::class)
      ->add('city', TextType::class)
      ->add('logo', FileType::class, array('label' => 'Logo de l\'équipe'))
      ->add('submit', SubmitType::class, array(
        'label' => 'Enregistrer',
        'attr' => array('class' => 'btn btn-primary btn-xs')
      ))
      ->getForm();

      $teamform->handleRequest($request);
      // méthode permettant de savoir si le formulaire a été soumis, équivalent $request->getMethod() == 'POST' lorsqu'on utilise l'objet $request de la classe Request.
    if($teamform->isSubmitted()) {

      $team = $teamform->getData(); //permet l'hydratation automatique
      $logo = $team->getLogo();
      $logoname = 'logo_'. strtolower($team->getName()) . '.' . $logo->guessExtension();

      $logo->move($this->getParameter('dir_logo'), $logoname);
      $team->setLogo($logoname);

      //enregistrement en db
      $em = $this->getDoctrine()->getManager();
      $em->persist($team);
      $em->flush();

      return $this->redirectToRoute('teams_index');
    }
        return $this->render('AppBundle:Teams:add.html.twig', array(
              'teamform' => $teamform->createView()
        ));
    }

    /**
     * @Route("/delete/{id}", name="teams_delete")
     */
    public function deleteAction($id)
    {
      $team = $this->getDoctrine()->getRepository(Teams::class)->find($id);
      $em = $this->getDoctrine()->getManager();
      $em->remove($team);
      $em->flush();

        return $this->redirectToRoute('teams_index');
    }

    /**
     * @Route("/edit/{id}", name="teams_edit")
     */
    public function editAction($id, Request $request) {
      $em = $this->getDoctrine()->getManager();
      $team = $em->getRepository(Teams::class)->find($id);
      //appeler getRepository depuis getManager établit une connexion, une visibilité entre le repo et le manager. Ici, le manager est au courant, est notifié de l'existence de l'objet fruit, si cet objet change (cad reçoit de nouvelles valeurs), le manager le sait. Le manager surveille cet objet.
      $teamformEdit = $this->createFormBuilder($team)
      ->add('name', TextType::class)
      ->add('city', TextType::class)
      ->add('logo', FileType::class, array(
        'label' => 'Logo de l\'équipe',
        'data_class' => null
        ))
      ->add('submit', SubmitType::class, array(
        'label' => 'Mettre à jour',
        'attr' => array('class' => 'btn btn-primary btn-xs')
          ))
      ->getForm();

      $teamformEdit->handleRequest($request);
      if($request->getMethod() == 'POST') {
        $team = $teamformEdit->getData();

        $logo = $team->getLogo();
        $logoname = 'logo_'. strtolower($team->getName()) . '.' . $logo->guessExtension();
        $logo->move($this->getParameter('dir_logo'), $logoname);
        $team->setLogo($logoname);

        $em = $this->getDoctrine()->getManager();
        $em->persist($team);
        $em->flush();

         return $this->redirectToRoute('teams_index');
        } // le manager exécutera la reqûete sql appropriée si l'objet $fruit a été modifié.

        return $this->render('AppBundle:Teams:edit.html.twig', array(
        'teamformEdit' => $teamformEdit->createView()
        ));
    }

}
