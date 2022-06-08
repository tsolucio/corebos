<?php

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../src/Inflect/Inflect.php');

use Inflect\Inflect;

class InflectTest extends PHPUnit_Framework_TestCase
{
    public function testSingularize()
    {
        $inflections = array('ox' => 'ox',
                'cats' => 'cat',
                'oxen' => 'ox',
                'cats' => 'cat',
                'purses' => 'purse',
                'analyses' => 'analysis',
                'houses' => 'house',
                'sheep' => 'sheep',
                'buses' => 'bus',
                'uses' => 'use',
                'databases' => 'database',
                'quizzes' => 'quiz',
                'matrices' => 'matrix',
                'vertices' => 'vertex',
                'alias' => 'alias',
                'aliases' => 'alias',
                'octopi' => 'octopus',
                'axes' => 'axis',
                'axis' => 'axis',
                'crises' => 'crisis',
                'crisis' => 'crisis',
                'shoes' => 'shoe',
                'foes' => 'foe',
                'pianos' => 'piano',
                'wierdos' => 'wierdo',
                'toes' => 'toe',
                'banjoes' => 'banjo',
                'vetoes' => 'veto',
                'cows' => 'cow',
            );

        foreach ($inflections as $key => $value)
        {
            print "Testing $key singularizes to: $value\n";
            $this->assertEquals($value, Inflect::singularize($key));
        }

	print "\n";
    }

    public function testPluralize()
    {
        $inflections = array('oxen' => 'ox',
                'cats' => 'cat',
                'cats' => 'cat',
                'purses' => 'purse',
                'analyses' => 'analysis',
                'houses' => 'house',
                'sheep' => 'sheep',
                'buses' => 'bus',
                'axes' => 'axis',
                'uses' => 'use',
                'databases' => 'database',
                'quizzes' => 'quiz',
                'matrices' => 'matrix',
                'vertices' => 'vertex',
                'aliases' => 'aliases',
                'aliases' => 'alias',
                'octopi' => 'octopus',
                'axes' => 'axis',
                'crises' => 'crisis',
                'crises' => 'crises',
                'shoes' => 'shoe',
                'foes' => 'foe',
                'pianos' => 'piano',
                'wierdos' => 'wierdo',
                'toes' => 'toe',
                'banjos' => 'banjo',
                'vetoes' => 'veto',
                'cows' => 'cow',
                );
        foreach ($inflections as $key => $value)
        {
            print "Testing $value pluralizes to: $key\n";
            $this->assertEquals($key, Inflect::pluralize($value));
        }
	print "\n";
    }
}