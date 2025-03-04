<?php

namespace App\Tests\Controller;

use App\Entity\Medicament;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MedicamentControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $medicamentRepository;
    private string $path = '/medicament/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->medicamentRepository = $this->manager->getRepository(Medicament::class);

        foreach ($this->medicamentRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Medicament index');

    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'medicament[nom]' => 'Testing',
            'medicament[dosage]' => 'Testing',
            'medicament[frequence]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->medicamentRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Medicament();
        $fixture->setNom('My Title');
        $fixture->setDosage('My Title');
        $fixture->setFrequence('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Medicament');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Medicament();
        $fixture->setNom('Value');
        $fixture->setDosage('Value');
        $fixture->setFrequence('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'medicament[nom]' => 'Something New',
            'medicament[dosage]' => 'Something New',
            'medicament[frequence]' => 'Something New',
        ]);

        self::assertResponseRedirects('/medicament/');

        $fixture = $this->medicamentRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getDosage());
        self::assertSame('Something New', $fixture[0]->getFrequence());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Medicament();
        $fixture->setNom('Value');
        $fixture->setDosage('Value');
        $fixture->setFrequence('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/medicament/');
        self::assertSame(0, $this->medicamentRepository->count([]));
    }
}
