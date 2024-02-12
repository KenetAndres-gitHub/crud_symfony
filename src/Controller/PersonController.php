<?php

namespace App\Controller;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\PersonRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/person')]
class PersonController extends AbstractController
{
    #[Route('/', name: 'app_person_index', methods: ['GET'])]
    public function index(PersonRepository $personRepository): Response
    {
        return $this->render('person/index.html.twig', [
            'people' => $personRepository->findBy([],['id'=>'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_person_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $firstName = $request->request->get('first_name');
            $lastName = $request->request->get('last_name');
            $birthdate = $request->request->get('birthdate');
            
            // Procesar los datos, por ejemplo, crear una nueva entidad Person y guardarla
            $person = new Person();
            $person->setFirstName($firstName);
            $person->setLastName($lastName);
            $person->setBirthdate(new \DateTime($birthdate));
            
            $entityManager->persist($person);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_person_index');
        }
    
        return $this->render('person/new.html.twig');
    }

    #[Route('/{id}/edit', name: 'app_person_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Person $person, EntityManagerInterface $entityManager): Response
    {
         // Obtener el ID del parámetro de la solicitud
        $id = $request->get('id');
        if ($request->isMethod('POST')) {
            $firstName = $request->request->get('first_name');
            $lastName = $request->request->get('last_name');
            $birthdate = $request->request->get('birthdate');
            
            // Procesar los datos, por ejemplo, crear una nueva entidad Person y guardarla
            $person = $entityManager->getRepository(Person::class)->find($id);
            $person->setFirstName($firstName);
            $person->setLastName($lastName);
            $person->setBirthdate(new \DateTime($birthdate));
            
            $entityManager->persist($person);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_person_index');
            dump('enviado');
        }
    
       
    
        // Renderizamos la plantilla de edición con el formulario
        return $this->render('person/edit.html.twig', [
            'person' => $person,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_person_delete', methods: ['GET'])]
    public function delete(Request $request, Person $person, EntityManagerInterface $entityManager): Response
    {
        $token = $request->query->get('_token');

        // Validar el token CSRF
        if (!$this->isCsrfTokenValid('delete' . $person->getId(), $token)) {
            throw $this->createAccessDeniedException('Token CSRF inválido.');
        }

        // Procesar la eliminación
        $entityManager->remove($person);
        $entityManager->flush();

        return $this->redirectToRoute('app_person_index', [], Response::HTTP_SEE_OTHER);
    }
    
}
