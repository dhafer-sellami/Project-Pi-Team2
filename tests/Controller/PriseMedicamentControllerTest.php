<?php

namespace App\Tests\Controller;

use App\Entity\PriseMedicament;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PriseMedicamentControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $priseMedicamentRepository;
    private string $path = '/prise/medicament/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->priseMedicamentRepository = $this->manager->getRepository(PriseMedicament::class);

        foreach ($this->priseMedicamentRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PriseMedicament index');

    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'prise_medicament[dateHeurePrise]' => 'Testing',
            'prise_medicament[pris]' => 'Testing',
            'prise_medicament[patient]' => 'Testing',
            'prise_medicament[medicament]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->priseMedicamentRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new PriseMedicament();
        $fixture->setDateHeurePrise('My Title');
        $fixture->setPris('My Title');
        $fixture->setPatient('My Title');
        $fixture->setMedicament('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PriseMedicament');

        
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new PriseMedicament();
        $fixture->setDateHeurePrise('Value');
        $fixture->setPris('Value');
        $fixture->setPatient('Value');
        $fixture->setMedicament('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'prise_medicament[dateHeurePrise]' => 'Something New',
            'prise_medicament[pris]' => 'Something New',
            'prise_medicament[patient]' => 'Something New',
            'prise_medicament[medicament]' => 'Something New',
        ]);

        self::assertResponseRedirects('/prise/medicament/');

        $fixture = $this->priseMedicamentRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getDateHeurePrise());
        self::assertSame('Something New', $fixture[0]->getPris());
        self::assertSame('Something New', $fixture[0]->getPatient());
        self::assertSame('Something New', $fixture[0]->getMedicament());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new PriseMedicament();
        $fixture->setDateHeurePrise('Value');
        $fixture->setPris('Value');
        $fixture->setPatient('Value');
        $fixture->setMedicament('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/prise/medicament/');
        self::assertSame(0, $this->priseMedicamentRepository->count([]));
    }
}
