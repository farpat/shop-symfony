<?php

namespace App\DataFixtures;

use App\Services\DataFixtures\Fixture;

class AppFixtures extends Fixture
{
    public function getDependencies()
    {
        return [ModuleFixtures::class, CategoryFixtures::class];
    }
}
