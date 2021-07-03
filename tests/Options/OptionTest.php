<?php

namespace Boscho87\tests\Options;

use Boscho87\ChangelogChecker\Options\Option;
use Boscho87\tests\BaseTestCase;

/**
 * Class OptionTest
 */
class OptionTest extends BaseTestCase
{
    /**
     * @group unit
     * @dataProvider optionsArray
     */
    public function testIfOptionsCanBeGet(array $option)
    {
        $options = new Option($option['check'], $option['error'], $option['fix'], $option);

        $this->assertEquals($option['check'], $options->isCheck());
        $this->assertEquals($option['error'], $options->isError());
        $this->assertEquals($option['fix'], $options->isFix());
        unset($option['check']);
        unset($option['error']);
        unset($option['fix']);

        foreach ($option as $key => $value) {
            $this->assertEquals($value, $options->$key);
        }
    }

    public function optionsArray(): array
    {
        return [
            [
                [
                    'check' => true,
                    'error' => true,
                    'fix' => true,
                    'odd-stuff' => true,
                ],
            ],
        ];
    }
}
