<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<style>
.fa-download.export {
    margin-right: 5px;
}

#sorting-buttons {
    list-style: none;
    margin-bottom: 30px;
    text-align: center;
}
#sorting-buttons li {
    display: inline;
}
#sorting-buttons a {
    display: inline-block;
}
@media (max-width: 500px) {
    #sorting-buttons a {
        width: 100%;
    }
}
@media (min-width: 501px) and (max-width: 790px) {
    #sorting-buttons a {
        width: 49%;
    }
}
@media (min-width: 991px) {
    #sorting-buttons {
        padding: 0;
    }
}

.page-activity table.ccm-search-results-table tbody tr:hover td {
    background: #b8e4f8;
    color: black;
}
.page-activity th.active {
    background: #00496e;
}
.page-activity th.active span {
    color: white;
}
@media (max-width: 767px) {
    .page-activity .table-responsive {
        border: none;
    }
}
@media (max-width: 992px) {
    div.ccm-dashboard-content-full.page-activity {
        margin-left: -20px !important;
        margin-right: -50px !important;
    }
}
@media (max-width: 1199px) {
    .page-activity table.ccm-search-results-table tbody tr td {
        padding-left: 10px;
        padding-right: 10px;
    }
}
</style>

<!-- action() is a form helper -->
<!-- the form helper action('reset') sets the form action to:
http://localhost/concrete5/index.php/dashboard/sitemap/page_activity/export
-->
<!-- on submit, the page opens to http://localhost/concrete5/index.php/dashboard/sitemap/page_activity/export and the controller method export() is called -->
<!--
Example:
<form method="post" action="http://localhost/concrete5/index.php/dashboard/sitemap/page_activity/export" id="938267752">
-->
<form method="post" action="<?php echo $controller->action('export'); ?>">
    <!-- TOKEN -->
    <!-- this token will be checked in the export() method in the single page controller -->
    <?php echo $this->controller->token->output('export'); ?>
    <!-- Export Page Activity button -->
    <div class="ccm-dashboard-header-buttons btn-group">
        <button class="btn btn-success" type="submit" name="action" value="export"><i class="fa fa-download export"></i><?php echo t('Export Page Activity'); ?></button>
    </div>
</form>

<ul id="sorting-buttons">
    <li><a class="btn btn-default" href="<?php echo URL::to('/dashboard/sitemap/page_activity', 'sortpages/dateadded'); ?>"><?php echo t('Sort By Date Added'); ?></a></li>
    <li><a class="btn btn-default" href="<?php echo URL::to('/dashboard/sitemap/page_activity', 'sortpages/datemodified'); ?>"><?php echo t('Sort By Date Modified'); ?></a></li>
    <li><a class="btn btn-default" href="<?php echo URL::to('/dashboard/sitemap/page_activity', 'sortpages/pageauthor'); ?>"><?php echo t('Sort By Page Author'); ?></a></li>
    <li><a class="btn btn-default" href="<?php echo URL::to('/dashboard/sitemap/page_activity', 'sortpages/modifiedauthor'); ?>"><?php echo t('Sort By Modified Author'); ?></a></li>
</ul>

<div class="ccm-dashboard-content-full page-activity">
    <div class="table-responsive">
        <table class="ccm-search-results-table">
            <thead>
                <tr>
                    <th><span><?php echo t('Page'); ?><br><?php echo t('Name'); ?></span></th>
                    <th><span><?php echo t('Page'); ?><br><?php echo t('Path'); ?></span></th>
                    <th <?php if ($active == 'dateadded') { echo 'class="active"'; } ?>><span><?php echo t('Date'); ?><br><?php echo t('Added'); ?></span></th>
                    <th <?php if ($active == 'datemodified') { echo 'class="active"'; } ?>><span><?php echo t('Date'); ?><br><?php echo t('Modified'); ?></span></th>
                    <th <?php if ($active == 'pageauthor') { echo 'class="active"'; } ?>><span><?php echo t('Page'); ?><br><?php echo t('Author'); ?></span></th>
                    <th <?php if ($active == 'modifiedauthor') { echo 'class="active"'; } ?>><span><?php echo t('Modified'); ?><br><?php echo t('Author'); ?></span></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($pages as $page) {
                    $tableRows = '<tr>';

                    $title = $page->getCollectionName();
                    $tableRows .= "<td>$title</td>";

                    $pageLink = $page->getCollectionLink();
                    $pagePath = $page->getCollectionPath();
                    $tableRows .= "<td><a href=\"$pageLink\">$pagePath</a></td>";

                    $dateService = Core::make('date');

                    $dateAdded = $page->getCollectionDateAdded();
                    $tableRows .= '<td>' . $dateService->formatDateTime($dateAdded, false) . '</td>';

                    $dateModified = $page->getCollectionDateLastModified();
                    $tableRows .= '<td>' . $dateService->formatDateTime($dateModified, false) . '</td>';

                    $pageAuthorID = $page->getCollectionUserID();
                    $pageAuthor = UserInfo::getByID($pageAuthorID);
                    if (is_object($pageAuthor)) {
                        $pageAuthorName = $pageAuthor->getUserName();
                    } else {
                        $pageAuthorName = "user deleted";
                    }
                    $tableRows .= "<td>$pageAuthorName</td>";

                    $pageUserModifiedByID = $page->getVersionObject()->getVersionAuthorUserID();
                    $pageUserModifiedBy = UserInfo::getByID($pageUserModifiedByID);
                    if (is_object($pageUserModifiedBy)) {
                        $pageUserModifiedByName = $pageUserModifiedBy->getUserName();
                    } else {
                        $pageUserModifiedByName = "user deleted";
                    }
                    $tableRows .= "<td>$pageUserModifiedByName</td>";
                    $tableRows .= '</tr>';

                    echo $tableRows;
                }
                ?>
            </tbody>
        </table>
        <div class="ccm-search-results-pagination">
            <?php
            echo $renderedPagination;
            ?>
        </div>
    </div>
</div>
