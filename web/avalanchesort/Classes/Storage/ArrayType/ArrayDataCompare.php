<?php
namespace Porthd\Avalanchesort\Storage\ArrayType;

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
class ArrayDataCompare implements DataCompareInterface
{

    protected $key = 'testKey';

    public function __construct(string $key = 'testKey')
    {
        if (empty($key)) {
            throw new UnexpectedValueException(
                'The value of `key` must be a not empty string in this `DataCompareClass`.',
                1592921675
            );
        }
        $this->key = $key;
    }


    public function compare($odd, $even): bool
    {
        return ($odd[$this->key] <= $even[$this->key]);
    }

}

