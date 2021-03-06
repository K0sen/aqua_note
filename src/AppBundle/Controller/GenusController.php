<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Genus;
use AppBundle\Entity\GenusNote;
use AppBundle\Services\MarkdownTransformer;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class GenusController extends Controller
{
    /**
     * @Route("/genus/new")             //TODO services public (51) & prod site version & markdown extension exists(53)
     */
    public function newAction()
    {
        $genus = new Genus();
        $genus->setName('Octopus'.random_int(1, 100));
        $genus->setSubFamily('Octopodinae');
        $genus->setSpeciesCount(rand(1, 666));

        $note = new GenusNote();
        $note->setUsername('AquaWeaver');
        $note->setUserAvatarFilename('ryan.jpeg');
        $note->setNote('I counted 8 legs... as they wrapped around me');
        $note->setCreatedAt(new \DateTime('-1 month'));
        $note->setGenus($genus);

        $em = $this->getDoctrine()->getManager();
        $em->persist($genus);
        $em->persist($note);
        $em->flush();

        return new Response('<html><body>Genus created</body></html>');
    }

    /**
     * @Route("/genus", name="genus")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $genuses = $em->getRepository('AppBundle:Genus')
                ->findAllPublishedOrderByRecentlyActive();

        return $this->render('genus/list.html.twig', [
            'genuses' => $genuses,
        ]);
    }

    /**
     * @Route("/genus/{genusName}", name="genus_show")
     */
    public function showAction($genusName, MarkdownTransformer $markdownTransformer, LoggerInterface $logger)
    {
        $em = $this->getDoctrine()->getManager();
        $genus = $em->getRepository('AppBundle:Genus')
                ->findOneBy(['name' => $genusName]);

        if (!$genus) {
            throw $this->createNotFoundException('No genus found');
        }

        $funFact = $markdownTransformer->parse($genus->getFunFact());

        $logger->info('Showing genus: '. $genusName);

        $recentNotes = $em->getRepository('AppBundle:GenusNote')
            ->findAllRecentNoteForGenus($genus);

        return $this->render('genus/show.html.twig', [
            'genus' => $genus,
            'funFact' => $funFact,
            'recentNoteCount' => count($recentNotes)
        ]);
    }

    /**
     * @Route("/genus/{name}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction(Genus $genus)
    {
        $notes = [];
        foreach ($genus->getNotes() as $note) {
            $notes[] = [
                'id' => $note->getId(),
                'username' => $note->getUsername(),
                'avatarUri' => '/images/'.$note->getUserAvatarFilename(),
                'note' => $note->getNote(),
                'date' => $note->getCreatedAt()->format('M d, Y')
            ];
        }

        $data = [
            'notes' => $notes,
        ];

        return new JsonResponse($data);
    }
}