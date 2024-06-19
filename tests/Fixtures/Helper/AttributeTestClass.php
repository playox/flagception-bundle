<?php

namespace Flagception\Tests\FlagceptionBundle\Fixtures\Helper;

use Flagception\Bundle\FlagceptionBundle\Attribute\Feature;

/**
 * Class AnnotationTestClass
 *
 * @author Michel Chowanski <michel.chowanski@bestit-online.de>
 * @package Flagception\Tests\FlagceptionBundle\Fixtures\Helper
 */
#[Feature("feature_abc")]
class AttributeTestClass
{
    /**
     * Normal test method
     *
     * @return void
     */
    public function normalMethod()
    {
    }

    /**
     * Valid test method
     *
     * @return void
     */
    public function validMethod()
    {
    }

    /**
     * Method with feature flag
     */
    #[Feature("feature_def")]
    public function invalidMethod()
    {
    }
}
