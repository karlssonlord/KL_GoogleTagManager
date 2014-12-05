<?php

namespace spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class KL_GoogleTagManager_Helper_DataSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('KL_GoogleTagManager_Helper_Data');
    }
}
