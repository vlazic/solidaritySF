<?php

namespace App\Tests\Form\Admin;

use App\Entity\City;
use App\Entity\SchoolType as EntitySchoolType;
use App\Form\Admin\SchoolSearchType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\DoctrineOrmExtension;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;

class SchoolSearchTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        // Mock entity manager
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        // Set up city repository mock
        $cityRepository = $this->createMock(EntityRepository::class);
        
        // Set up school type repository mock
        $schoolTypeRepository = $this->createMock(EntityRepository::class);
        
        // Configure the entity manager to return our repository mocks
        $entityManager->expects($this->any())
            ->method('getRepository')
            ->willReturnMap([
                [City::class, $cityRepository],
                [EntitySchoolType::class, $schoolTypeRepository]
            ]);
        
        // Set up the registry mock
        $mockRegistry = $this->createMock(ManagerRegistry::class);
        $mockRegistry->expects($this->any())
            ->method('getManagerForClass')
            ->willReturn($entityManager);
        
        return [
            new DoctrineOrmExtension($mockRegistry),
        ];
    }

    public function testSchoolSearchFormHasCorrectFields(): void
    {
        $form = $this->factory->create(SchoolSearchType::class);
        
        // Check that form contains the expected fields
        $this->assertTrue($form->has('name'));
        $this->assertTrue($form->has('city'));
        $this->assertTrue($form->has('type'));
        $this->assertTrue($form->has('submit'));
        
        // Get the form field types
        $this->assertInstanceOf(TextType::class, $form->get('name')->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(EntityType::class, $form->get('city')->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(EntityType::class, $form->get('type')->getConfig()->getType()->getInnerType());
        $this->assertInstanceOf(SubmitType::class, $form->get('submit')->getConfig()->getType()->getInnerType());
        
        // Check that fields are not required
        $this->assertFalse($form->get('name')->getConfig()->getOption('required'));
        $this->assertFalse($form->get('city')->getConfig()->getOption('required'));
        $this->assertFalse($form->get('type')->getConfig()->getOption('required'));
        
        // Check that form method is GET
        $this->assertEquals('GET', $form->getConfig()->getMethod());
    }
    
    public function testConfigureOptions(): void
    {
        $form = $this->factory->create(SchoolSearchType::class);
        $options = $form->getConfig()->getOptions();
        
        // Test CSRF protection and validation groups
        $this->assertFalse($options['csrf_protection']);
        $this->assertFalse($options['validation_groups']);
    }
    
    public function testGetBlockPrefix(): void
    {
        $form = $this->factory->create(SchoolSearchType::class);
        $formType = new SchoolSearchType();
        
        // The block prefix should be empty string
        $this->assertEquals('', $formType->getBlockPrefix());
    }
}