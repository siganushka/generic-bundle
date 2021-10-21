<?php

declare(strict_types=1);

namespace Siganushka\GenericBundle\Tree\Exception;

use Siganushka\GenericBundle\Tree\TreeNodeInterface;

class ParentConflictException extends \RuntimeException
{
    protected $current;
    protected $parent;

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
