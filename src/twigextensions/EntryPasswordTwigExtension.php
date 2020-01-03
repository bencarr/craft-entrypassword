<?php

namespace bencarr\entrypassword\twigextensions;

use Twig\Extension\AbstractExtension;

class EntryPasswordTwigExtension extends AbstractExtension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Entry Password';
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [];
    }
}
