<?php

namespace Paraunit\Tests\Unit;


/**
 * Class DisTest
 * @package Paraunit\Tests\Unit
 */
class DisTest extends \PHPUnit_Framework_TestCase
{
    private $distances = [];
//    private $permutations = [];

    /**
     * DisTest constructor.
     */
    public function __construct()
    {
        $this->distances['Tristram']['AlphaCentauri'] = 34;
        $this->distances['Tristram']['Snowdin'] = 100;
        $this->distances['Tristram']['Tambi'] = 63;
        $this->distances['Tristram']['Faerun'] = 108;
        $this->distances['Tristram']['Norrath'] = 111;
        $this->distances['Tristram']['Straylight'] = 89;
        $this->distances['Tristram']['Arbre'] = 132;
        $this->distances['AlphaCentauri']['Snowdin'] = 4;
        $this->distances['AlphaCentauri']['Tambi'] = 79;
        $this->distances['AlphaCentauri']['Faerun'] = 44;
        $this->distances['AlphaCentauri']['Norrath'] = 147;
        $this->distances['AlphaCentauri']['Straylight'] = 133;
        $this->distances['AlphaCentauri']['Arbre'] = 74;
        $this->distances['Snowdin']['Tambi'] = 105;
        $this->distances['Snowdin']['Faerun'] = 95;
        $this->distances['Snowdin']['Norrath'] = 48;
        $this->distances['Snowdin']['Straylight'] = 88;
        $this->distances['Snowdin']['Arbre'] = 7;
        $this->distances['Tambi']['Faerun'] = 68;
        $this->distances['Tambi']['Norrath'] = 134;
        $this->distances['Tambi']['Straylight'] = 107;
        $this->distances['Tambi']['Arbre'] = 40;
        $this->distances['Faerun']['Norrath'] = 11;
        $this->distances['Faerun']['Straylight'] = 66;
        $this->distances['Faerun']['Arbre'] = 144;
        $this->distances['Norrath']['Straylight'] = 115;
        $this->distances['Norrath']['Arbre'] = 135;
        $this->distances['Straylight']['Arbre'] = 127;
    }

    public function testTry()
    {
        $cities = [
            'Tristram',
            'AlphaCentauri',
            'Snowdin',
            'Tambi',
            'Faerun',
            'Norrath',
            'Straylight',
            'Arbre',
        ];

        $permutations = $this->pc_permute($cities);

        $maxDist = 0;

        foreach ($permutations as $permutation) {
            $distance = 0;
            $prevCity = null;
            foreach ($permutation as $city) {
                if ($prevCity) {
                    $distance += $this->getDistance($prevCity, $city);
                }
                $prevCity = $city;

            }

            if ($distance > $maxDist && $distance != 0) $maxDist = $distance;
        }

        $this->assertEquals(38, $maxDist);
    }

    private function pc_permute($items, $perms = array( ))
    {
        if (empty($items)) {
            $return = array($perms);
        }  else {
            $return = array();
            for ($i = count($items) - 1; $i >= 0; --$i) {
                $newitems = $items;
                $newperms = $perms;
                list($foo) = array_splice($newitems, $i, 1);
                array_unshift($newperms, $foo);
                $return = array_merge($return, $this->pc_permute($newitems, $newperms));
            }
        }
        return $return;
    }
    
    private function getDistance($a, $b)
    {
        if (isset($this->distances[$a][$b])) {
            return $this->distances[$a][$b];
        }

        return $this->distances[$b][$a];
    }

    public function citiesProvider()
    {
        return [
            [array(), 0],
        ];
//        return [
//            [[], 0],
//            [
//                [
//                    'Tristram',
//                    'AlphaCentauri',
//                    'Snowdin',
//                ], 38
//
//            ],
//            [
//                [
//                    'Tristram',
//                    'AlphaCentauri',
//                    'Snowdin',
//                    'Tambi',
//                    'Faerun',
//                    'Norrath',
//                    'Straylight',
//                ], 0
//
//            ],
//        ];
    }

}
