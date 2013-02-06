<?php
use Flickering\Flickering;

abstract class FlickeringTests extends PHPUnit_Framework_TestCase
{
  protected function getFlickering()
  {
    return new Flickering('foo', 'bar');
  }
}