<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Thématiques philosophiques', null, $manager);

        $this->createCategory('Métaphysique', $parent, $manager);
        $this->createCategory('Éthique', $parent, $manager);
        $this->createCategory('Politique', $parent, $manager);
        $this->createCategory('Religion', $parent, $manager);

        $parent = $this->createCategory('Périodes philosophiques', null, $manager);

        $this->createCategory('Antique', $parent, $manager);
        $this->createCategory('Médiévale', $parent, $manager);
        $this->createCategory('Moderne', $parent, $manager);
        $this->createCategory('Contemporaine', $parent, $manager);

        $manager->flush();
    }

    public function createCategory(string $name, Categories $parent = null, ObjectManager $manager)
    {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $manager->persist($category);

        return $category;
    }
}