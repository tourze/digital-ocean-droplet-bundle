<?php

namespace DigitalOceanDropletBundle\Tests\Integration\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DropletCrudControllerTest extends WebTestCase
{
    public function testGetEntityFqcn(): void
    {
        $this->assertEquals('DigitalOceanDropletBundle\Entity\Droplet', 
            \DigitalOceanDropletBundle\Controller\Admin\DropletCrudController::getEntityFqcn());
    }
}