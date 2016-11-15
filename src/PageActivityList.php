<?php
namespace Concrete\Package\PageActivity\Src;

use Concrete\Core\Page\PageList;

class PageActivityList extends PageList
{
    public function sortByDateModifiedDescending()
    {
        $this->query->orderBy('cDateModified', 'desc');
    }

    public function sortByDateAddedDescending()
    {
        $this->query->orderBy('cDateAdded', 'desc');
    }

    public function sortByAuthorModifiedAscending()
    {
        // addOrderBy() adds a secondary sort
        $this->query->orderBy('cvAuthorUID', 'asc')->addOrderBy('cDateModified', 'desc');
    }

    public function sortByAuthorIDAscending()
    {
        // addOrderBy() adds a secondary sort
        $this->query->orderBy('uID', 'asc')->addOrderBy('cDateAdded', 'desc');
    }
}
