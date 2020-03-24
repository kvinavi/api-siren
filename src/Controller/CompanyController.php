<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Company;

class CompanyController extends AbstractController
{
    /**
    * @Route("/company/{siren}", name="get_company", methods={"GET"})
    */
    public function getCompany($siren): JsonResponse
    {
        // Get company using SIREN number
        $company = $this->getDoctrine()->getRepository(Company::class)->findOneBy(['siren' => $siren]);
        //If company exist in database, display company informations, else display 404 NOT FOUND
        if ($company){
            return new JsonResponse($company->toArray(), Response::HTTP_OK);
        }
        return new JsonResponse(['response' => 'Ce numéro SIREN n\'est pas attribué'], Response::HTTP_NOT_FOUND);
    }

    /**
    * @Route("/companies/update", name="update_companies", methods={"POST"})
    */
    public function updateCompanies(): JsonResponse
    {
        
        $em = $this->getDoctrine()->getManager();
        //Return error if file not found
        if (!$_FILES || $_FILES['file']['tmp_name'] === ""){
            return new JsonResponse(['response' => 'Fichier manquant'], Response::HTTP_NOT_FOUND);
        }
        // Get post csv file which contains updated companies and convert each CSV line to array
        if (($file = fopen($_FILES['file']['tmp_name'], 'r')) !== FALSE) {
            // Used to verify if this is the first csv file's line
            $isFirstLineFile = true;
            while (($data = fgetcsv($file, 1000, ";")) !== FALSE) {
                // if is first line, do nothing except change to false in order to execute code for other lines
                if ($isFirstLineFile === true){
                    $isFirstLineFile = false;
                }else{
                    $company = $this->getDoctrine()->getRepository(Company::class)->findOneBy(array('siren' => $data[0]));
                    if (!$company) {
                        $company = new Company();
                        $company->setSiren((int)$data[0]);
                        $company->setCreationDate(new \DateTime());
                        $em->persist($company);
                    }
                    $company->setUpdateDate(new \DateTime());
                    $company->setName($data[2]);
                    $company->setAddress($data[12]);
                    $company->setCity($data[14]);
                    $em->flush();
                }
            }
            fclose($file);
            return new JsonResponse(['response' => 'Mise à jour effectuée'], Response::HTTP_OK);
        }
        return new JsonResponse(['response' => 'Fichier manquant'], Response::HTTP_NO_FOUND);
    }
}
