<?php
namespace Concrete\Package\PageActivity\Controller\SinglePage\Dashboard\Sitemap;

use \Concrete\Core\Page\Controller\DashboardPageController;
use \Concrete\Package\PageActivity\Src\PageActivityList;
use Core;
use UserInfo;

class PageActivity extends DashboardPageController
{
    public function view()
    {
        $this->sortPages();
    }

    public function sortPages($sortBy = 'datemodified')
    {
        $list = new PageActivityList();

        if ($sortBy == 'dateadded') {
            $list->sortByDateAddedDescending();
        } elseif ($sortBy == 'datemodified') {
            $list->sortByDateModifiedDescending();
        } elseif ($sortBy == 'pageauthor') {
            $list->sortByAuthorIDAscending();
        } elseif ($sortBy == 'modifiedauthor') {
            $list->sortByAuthorModifiedAscending();
        } else {
            $list->sortByDateModifiedDescending();
        }

        $list->setItemsPerPage(10);
        $pages = $list->getResults();
        $pagination = $list->getPagination();
        $pages = $pagination->getCurrentPageResults();
        $renderedPagination = $pagination->renderDefaultView();

        $this->set('active', $sortBy);
        $this->set('pages', $pages);
        $this->set('renderedPagination', $renderedPagination);
    }

    public function export()
    {
        // this validates the hash - a specific time, user, and action
        // http://www.concrete5.org/api/class-Concrete.Core.Validation.CSRF.Token.html
        // adds an encrypted token to the page, then checks for that encrypted token when handling input from that page, validating that the input is genuine
        // - used when a block or dashboard page takes input and acts on that input, perhaps making a change to the database
        // - helps determine if a form or ajax submission has genuinely come from your block or page view and not from a malicious party
        // http://www.concrete5.org/documentation/how-tos/developers/use-tokens-to-secure-transactions/
        if ($this->token->validate('export')) {
            // - set the content type to the text/csv mime type
            header('Content-type: text/csv');
            // no-cache - don't cache the file
            // no-store - don't store the response
            // must-revalidate - revalidate a cached asset on following requests
            header('Cache-Control: no-cache, no-store, must-revalidate');
            // "Pragma: no-cache" is interpreted as "Cache-Control: no-cache"
            // Pragma is used by older browsers
            header('Pragma: no-cache');
            // a new file must created each time
            header('Expires: 0');
            // force the content of the page to be downloaded as a file (otherwise it is displayed in the browser)
            header('Content-Disposition: attachment; filename="page_activity_export_' . date('m-d-Y') . '.csv"');

            // fopen() opens a file or URL - binds the resource, specified by filename, to a stream
            // - w - create a write-only output buffer
            $output = fopen('php://output', 'w');

            // create the column headers array
            $columnHeaders = array('Page Name', 'Page Path', 'Date Added', 'Date Modified', 'Page Author', 'Modified Author');
            // use PHP's fputcsv function to ensure CSV formatting
            // it formats a line passed as a fields array as CSV and writes it to the specified file
            // - the first parameter is the file opened
            // - the second parameter is an array of fields
            // fputcsv() doesn't appear to be required to ouput a CSV, but does handle the comma separation and end of line formatting
            // - comma separated values could be echoed to the page and would still work, but that requires adding the commas and ending the line each time
            fputcsv($output, $columnHeaders);


            $list = new PageActivityList();
            $list->sortByDateModifiedDescending();
            $pages = $list->getResults();

            // loop through the page list results object
            foreach ($pages as $page) {
                $pageName = $page->getCollectionName();
                $pagePath = $page->getCollectionPath();

                $dateService = Core::make('date');
                $pageDateAdded = $dateService->formatDateTime($page->getCollectionDateAdded(), false);
                $pageDateModified = $dateService->formatDateTime($page->getCollectionDateLastModified(), false);
                // $pageDateAdded = $page->getCollectionDateAdded();
                // $pageDateModified = $page->getCollectionDateLastModified();

                $pageAuthorID = $page->getCollectionUserID();
                $pageAuthor = UserInfo::getByID($pageAuthorID);
                if (is_object($pageAuthor)) {
                    $pageAuthorName = $pageAuthor->getUserName();
                } else {
                    $pageAuthorName = 'user deleted';
                }

                $pageUserModifiedByID = $page->getVersionObject()->getVersionAuthorUserID();
                $pageUserModifiedBy = UserInfo::getByID($pageUserModifiedByID);
                if (is_object($pageUserModifiedBy)) {
                    $pageUserModifiedByName = $pageUserModifiedBy->getUserName();
                } else {
                    $pageUserModifiedByName = 'user deleted';
                }

                $data = array($pageName, $pagePath, $pageDateAdded, $pageDateModified, $pageAuthorName, $pageUserModifiedByName);
                fputcsv($output, $data);
            }

            // close the file
            fclose($output);

            // end the script
            exit();
        } else {
            // if the token doesn't validate, the error message is set with an array of validation error messages
            $this->set('error', array($this->token->getErrorMessage()));
        }
    }
}
