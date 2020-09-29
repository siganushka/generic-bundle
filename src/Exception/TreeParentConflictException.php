<?php

namespace App\Exception;

use App\Tree\NodeInterface;

class TreeParentConflictException extends \RuntimeException
{
    private $current;
    private $parent;

    public function __construct(NodeInterface $current, NodeInterface $parent)
    {
        $this->current = $current;
        $this->parent = $parent;

        parent::__construct('The tree node parent conflict has been detected.');
    }

    public function getCurrent(): NodeInterface
    {
        return $this->current;
    }

    public function getParent(): NodeInterface
    {
        return $this->parent;
    }
}
