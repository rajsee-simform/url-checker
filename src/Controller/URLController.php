<?php

namespace App\Controller;

use App\Form\URLType;
use App\Service\URLService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class URLController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/", name="url_index", methods={"GET", "POST"})
     */
    public function index(Request $request, URLService $urlService): Response
    {
        $form = $this->createForm(URLType::class);
        $form->handleRequest($request);

        $message = $errorMessage = '';
        if ($form->isSubmitted() && $form->isValid()) {

            // Get the uploaded file
            $csvFile = $form->get('file_uploader')->getData();
            $csvData = file_get_contents($csvFile->getPathName());
            $urls = explode(PHP_EOL, $csvData);

            $result = $urlService->checkUrlsExist($urls);
            if ($result['isDuplicate']) {
                $errorMessage = 'Some or all the URLs already exist. Record(s) Inserted : '.$result["insertedRecords"].' Duplicate Record(s) : '.$result["duplicateRecords"];
            }else{
                $message = $result["insertedRecords"] ." URL(s) have been inserted";
            }
        }

        return $this->render('url/index.html.twig', [
            'form' => $form->createView(),
            'message' => $message,
            'errorMessage' => $errorMessage
        ]);
    }
}
