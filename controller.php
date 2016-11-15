<?php
namespace Concrete\Package\PageActivity;

use Package;
use SinglePage;

class Controller extends Package
{
    protected $pkgHandle = 'page_activity';
    protected $appVersionRequired = '5.7.4';
    protected $pkgVersion = '0.9.3';

    public function getPackageDescription()
    {
        return t('View recent page activity by date added, date modified, page author, and modified author.');
    }

    public function getPackageName()
    {
        return t('Page Activity');
    }

    public function install()
    {
        $pkg = parent::install();

        SinglePage::add('/dashboard/sitemap/page_activity', $pkg);
    }
}
