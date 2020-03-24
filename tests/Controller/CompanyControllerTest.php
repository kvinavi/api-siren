<?php
// tests/Controller/CompanyControllerTest.php
namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CompanyControllerTest extends WebTestCase
{

    public function testGetCompanyWhichExists()
    {
        //Test company query with valid siren
        $client = static::createClient();
        $client->request('GET', '/company/15950082');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testGetCompanyWhichDoesNotExist()
    {
        //Test company query with non existing siren
        $client = static::createClient();
        $client->request('GET', '/company/1');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testGetCompanyWhithNoSirenDefined()
    {
        //Test company query with no siren defined
        $client = static::createClient();
        $client->request('GET', '/company/');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testUpdateCompaniesWhithoutNewCompany()
    {
        //Test company update in a file which does not contain a new company : only update
        $client = static::createClient();
        $_FILES = [
            'file' => [
                'name' => dirname( __FILE__, 2) . '/testFiles/test.csv',
                'type' => 'text/csv',
                'tmp_name' => dirname( __FILE__, 2) . '/testFiles/test.csv',
                'error' => 0,
                'size' => 4694,
            ]
        ];
        $client->request(
            'POST',
            '/companies/update',
            [],
            [$_FILES]
        );

        $this->assertEquals("Mise à jour effectuée", json_decode($client->getResponse()->getContent())->response);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUpdateCompaniesWithNewCompany()
    {
        //Test company update in a file which contains a new company : create and update
        $client = static::createClient();
        // In order to have a new company to create, we generate a new test company in the test.csv file, with a random siren number.
        $file = fopen(dirname( __FILE__, 2) . '/testFiles/test.csv', "a");
        fputcsv($file, [rand(10, 10000), "", "Name", "", "", "", "", "", "", "", "", "", "Address", "", "City"], ";");
        fclose($file);
        $_FILES = [
            'file' => [
                'name' => dirname( __FILE__, 2) . '/testFiles/test.csv',
                'type' => 'text/csv',
                'tmp_name' => dirname( __FILE__, 2) . '/testFiles/test.csv',
                'error' => 0,
                'size' => 4694,
            ]
        ];
        $client->request(
            'POST',
            '/companies/update',
            [],
            [$_FILES]
        );

        $this->assertEquals("Mise à jour effectuée", json_decode($client->getResponse()->getContent())->response);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUpdateCompaniesWithoutFile()
    {
        //Test company update when no file is defined
        $client = static::createClient();
        $_FILES = [];
        $client->request(
            'POST',
            '/companies/update',
            [],
            [$_FILES]
        );
        
        $this->assertEquals("Fichier manquant", json_decode($client->getResponse()->getContent())->response);
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

}