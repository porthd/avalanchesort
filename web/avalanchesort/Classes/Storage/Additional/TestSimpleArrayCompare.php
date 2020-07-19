<?php
namespace Porthd\Avalanchesort\Storage\Additional;

use Porthd\Avalanchesort\Defs\DataCompareInterface;
use UnexpectedValueException;

/***
 *
 * This file is part of the "Icon List" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020 Dr. Dieter Porth <info@mogber.de>
 *
 ***/
class TestSimpleArrayCompare implements DataCompareInterface
{

    protected $key = 'testKey';

    public function __construct(string $key = 'testKey')
    {
        if (empty(trim($key))) {
            throw new UnexpectedValueException(
                'The value of `key` must be a trimmed, not empty string in this `DataCompareClass`.',
                1592921675
            );
        }
        $this->key = $key;
    }


    public function changeTestKey($key)
    {
        if (empty(trim($key))) {
            throw new UnexpectedValueException(
                'The value of `key` must be a trimmed, not empty string in this `DataCompareClass`.',
                1592921675
            );
        }
        $this->key = trim($key);
    }

    public function compare($odd, $even): bool
    {
        return ($odd[$this->key] <= $even[$this->key]);
    }

}

