<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contacts;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Contact controller.
 *
 * @Route("/admin/contacts")
 * * @Security("has_role('ROLE_USER')")
 */
class ContactsController extends Controller
{
    /**
     * Lists all contact entities.
     *
     * @Route("/", name="contacts_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $contacts = $em->getRepository('AppBundle:Contacts')->findAll();

        return $this->render('contacts/index.html.twig', array(
            'contacts' => $contacts,
        ));
    }

    /**
     * Creates a new contact entity.
     *
     * @Route("/new", name="contacts_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $contact = new Contacts();
        $form = $this->createForm('AppBundle\Form\ContactsType', $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            return $this->redirectToRoute('contacts_show', array('id' => $contact->getId()));
        }

        return $this->render('contacts/new.html.twig', array(
            'contact' => $contact,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a contact entity.
     *
     * @Route("/{id}", name="contacts_show")
     * @Method("GET")
     */
    public function showAction(Contacts $contact)
    {
        $deleteForm = $this->createDeleteForm($contact);

        return $this->render('contacts/show.html.twig', array(
            'contact' => $contact,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing contact entity.
     *
     * @Route("/{id}/edit", name="contacts_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Contacts $contact)
    {
        $deleteForm = $this->createDeleteForm($contact);
        $editForm = $this->createForm('AppBundle\Form\ContactsType', $contact);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contacts_edit', array('id' => $contact->getId()));
        }

        return $this->render('contacts/edit.html.twig', array(
            'contact' => $contact,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a contact entity.
     *
     * @Route("/{id}", name="contacts_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Contacts $contact)
    {
        $form = $this->createDeleteForm($contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($contact);
            $em->flush();
        }

        return $this->redirectToRoute('contacts_index');
    }

    /**
     * Creates a form to delete a contact entity.
     *
     * @param Contacts $contact The contact entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Contacts $contact)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contacts_delete', array('id' => $contact->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
