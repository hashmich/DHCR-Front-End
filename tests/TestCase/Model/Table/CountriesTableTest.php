<?php

declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CountriesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CountriesTable Test Case
 */
class CountriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CountriesTable
     */
    protected $Countries;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Countries',
        'app.Cities',
        'app.Courses',
        'app.Institutions',
        'app.Subscriptions',
        'app.Users',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Countries') ? [] : ['className' => CountriesTable::class];
        $this->Countries = $this->getTableLocator()->get('Countries', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Countries);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\CountriesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
