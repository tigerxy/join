<?php

namespace Concrete\Package\Join;

defined('C5_EXECUTE') or die(_("Access Denied."));

use \Concrete\Core\Package\Package;
use \Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Support\Facade\Database;

class Controller extends Package
{
    protected $pkgHandle = 'join';
    protected $appVersionRequired = '8.5.4';
    protected $pkgVersion = '1.0';

    public function getPackageDescription()
    {
        return t('Adds a join block that registered users can use to signal their interest in joining.');
    }

    public function getPackageName()
    {
        return t('Join');
    }

    public function install()
    {
        $pkg = parent::install();
        $bt = BlockType::getByHandle('join');
        if (!is_object($bt)) {
            $bt = BlockType::installBlockType('join', $pkg);
        }
    }

    public function uninstall()
    {
        parent::uninstall();
        $db = Database::connection();
        $db->query('DROP TABLE btJoin');
        $db->query('DROP TABLE btJoinUser');
    }
}