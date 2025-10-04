<?php

declare(strict_types=1);

namespace Quantum\Test;

use Platine\Dev\PlatineTestCase;
use Quantum\Hub\App;

/**
 * App class tests
 *
 * @group core
 */
class AppTest extends PlatineTestCase
{
    public function testAll(): void
    {
        $o = new App();
        $this->assertInstanceOf(App::class, $o);
    }
}
