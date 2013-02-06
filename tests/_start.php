<?php
use Flickering\Flickering;
use Flickering\Method;

abstract class FlickeringTests extends PHPUnit_Framework_TestCase
{
  protected function getDummyFlickering()
  {
    return new Flickering('foo', 'bar');
  }

  protected function getDummyMethod()
  {
    return new Method($this->getDummyFlickering(), 'foobar', array('foo' => 'bar'));
  }
}