<?php

namespace App\Controller;

use App\Entity\Race;
use App\Entity\Result;
use App\Form\CreateRaceType;
use App\Form\EditResultType;
use App\Repository\ResultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;

class RacesController extends AbstractController
{
    // Route used for Results upload form
    #[Route('/upload', name: 'upload')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $race = new Race();

        $form = $this->createForm(CreateRaceType::class, $race);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            // Get data from csv file
            $csvFile = $form->get('csvFile')->getData();

            if ($csvFile) {

                $raceResults = $this->getCsvArray($csvFile);

                // Pull columns distance and time into separate arrays
                $distance  = array_column($raceResults, 'distance');
                $raceTime = array_column($raceResults, 'time');
                
                // Use pulled columns to sort by both, distance and time
                array_multisort($distance, SORT_DESC, $raceTime, SORT_ASC, $raceResults);
                
                // Find out and add race placement to db
                $place = 0;
                $placelong = 1;
                $placemedium = 1;
                
                // Separated results by distance
                foreach ($raceResults as $raceResult) {
                    $newResult = new Result();
                    switch($raceResult['distance']) {
                        case 'long':
                            $place = $placelong++;
                            break;
                        case 'medium':
                            $place = $placemedium++;
                            break;
                    }
                    $newResult->setFullName($raceResult['fullName']);
                    
                    $parsed = date_parse($raceResult['time']);

                    // Convert time to seconds and save to db
                    $seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
                    $newResult->setRaceTime($seconds);
                    
                    $newResult->setDistance($raceResult['distance']);
                    $newResult->setPlacement($place);
                    $newResult->setRace($race);

                    $entityManager->persist($newResult);


                }

            }

            $entityManager->persist($race);
            $entityManager->flush();

            // Error handling section
            $errorName = null;
            $errorTime = null;

            if ($form->get('raceName')->getData() == null) {
            
                $errorName = 'Race name error, empty value added';
            
            } else if ($form->get('csvFile')->getData() == null) {
            
                $errorTime = 'File error, file not added';
            
            }

            // Redirect on submit and send data to next route
            return $this->redirectToRoute('view', [
                'id' => $race->getId(),
                'errorName' => $errorName,
                'errorTime' => $errorTime
            ]);

        }

        return $this->render('upload.html.twig', [
            'race_form' => $form->createView(),
        ]);
    }

    // Handling/decoding csv files
    public function getCsvArray($csvFile)
    {
        $decoder = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        return $decoder->decode(file_get_contents($csvFile), 'csv');
    }

    // Route used for editing single result
    #[Route('/edit/{id}', name: 'edit')]
    public function edit($id, ResultRepository $resultRepo, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get single result by id
        $result = $resultRepo->find($id);
        
        $form = $this->createForm(EditResultType::class, $result);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($result);
            $entityManager->flush();

            // Error handling section
            $errorName = null;
            $errorTime = null;

            if ($form->get('fullName')->getData() == null) {
            
                $errorName = 'Full name error, empty value added';
            
            } else if ($form->get('raceTime')->getData() == null) {
            
                $errorTime = 'Finish time error, empty value added';
            
            }

            // Redirect to View route and send needed info
            return $this->redirectToRoute('view', [
                'id' => $result->getRace()->getId(),
                'errorName' => $errorName,
                'errorTime' => $errorTime
            ]);

        }

        return $this->render('edit.html.twig', [
            'result' => $result,
            'edit_form' => $form->createView(),
        ]);
    }

    // Route for displaying formatted results
    #[Route('/view/{id}', name: 'view', defaults: ['id' => null], methods:['GET', 'HEAD'])]
    public function view($id, ResultRepository $resultRepo): Response
    {

        // Find race results by provided id
        $race = $resultRepo->findBy(['race' => $id]);

        // Render race results preview
        return $this->render('view.html.twig', [
            'race' => $race
        ]);
    }

    // Route home. Only used as a starting point for app
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('index.html.twig');
    }

}
