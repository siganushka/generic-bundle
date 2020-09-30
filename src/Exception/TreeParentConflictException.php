<?php

namespace Siganushka\GenericBundle\Exception;

use Siganushka\GenericBundle\DataStructure\TreeNodeInterface;

class TreeParentConflictException extends \RuntimeException
{
    private $current;
    private $parent;

    public function __construct(TreeNodeInterface $current, TreeNodeInterface $parent)
    {
        $this->current = $current;
        $this->parent = $parent;

        parent::__construct('The tree node parent conflict has been detected.');
    }

    public function getCurrent(): TreeNodeInterface
    {
        return $this->current;
    }

    public function getParent(): TreeNodeInterface
    {
        return $this->parent;
    }
}
