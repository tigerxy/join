<?php

namespace Concrete\Package\Join\Block\Join;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Support\Facade\Application as Core;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Support\Facade\Database;
use Concrete\Core\User\User;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Validation\SanitizeService;

defined('C5_EXECUTE') or die(_("Access Denied."));

class Controller extends BlockController
{
    protected $btTable = "btJoin";
    private $btJoinUser = "btJoinUser";
    private $dbConn = null;

    public function getBlockTypeName()
    {
        return t('Join');
    }

    public function getBlockTypeDescription()
    {
        return t('Registered users can signal their interest to join.');
    }

    public function view()
    {
        $user = new User();
        $this->set('isRegistered', $user->isRegistered());
        $uId = $user->getUserID();

        $joined = $this->selectDb(["bID" => $this->bID]);
        $currentUserJoinedEntry = array_reduce($joined, function ($match, $join) {
            return $join['uID'] == $match['uID'] ? $join : $match;
        }, ['uID' => $uId, 'empty' => true]);
        $currentUserHasJoined = !array_key_exists('empty', $currentUserJoinedEntry);
        $this->set('currentUserHasJoined', $currentUserHasJoined);
        $this->set('currentUserComment', $currentUserHasJoined ? $currentUserJoinedEntry['comment'] : '');
        $joined = array_map(function ($join) {
            $user = new User();
            $userInfo = $user->getByUserID($join["uID"])->getUserInfoObject();
            return [
                "name" => $userInfo->getUserDisplayName(),
                "email" => $userInfo->getUserEmail(),
                "avatar" => $userInfo->getUserAvatar()->getPath(),
                "comment" => $join["comment"]
            ];
        }, $joined);
        $this->set('joined', $joined);
    }

    public function action_join($token = false, $bID = false)
    {
        if ($this->bID != $bID) {
            return false;
        }
        if (Core::make('token')->validate('join', $token)) {
            $this->join(true);
            $this->redirect2();
        }
        exit;
    }

    public function action_disjoin($token = false, $bID = false)
    {
        if ($this->bID != $bID) {
            return false;
        }
        if (Core::make('token')->validate('join', $token)) {
            $this->join(false);
            $this->redirect2();
        }
        exit;
    }

    public function action_comment($token = false, $bID = false)
    {
        if ($this->bID != $bID) {
            return false;
        }
        if (Core::make('token')->validate('join', $token)) {
            if (isset($_POST['comment'])) {
                $this->comment(h($_POST['comment']));
            }
            $this->redirect2();
        }
    }

    private function join(bool $join)
    {
        $o = $this->getIndentifiers();
        if ($join) {
            $this->insertDb($o);
        } else {
            $this->deleteDb($o);
        }
    }

    private function comment(string $comment)
    {
        $data = ["comment" => $comment];
        $identifier = $this->getIndentifiers();
        $this->updateDb($data, $identifier);
    }

    private function userJoined(string $uId = null)
    {
        if ($uId == null) {
            $u = new User();
            $uId = $u->getUserID();
        }
        $db = $this->getDb();
    }

    private function redirect2()
    {
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $b = $this->getBlockObject();
            $bv = new BlockView($b);
            $bv->render('view');
        } else {
            $page = Page::getCurrentPage();
            Redirect::page($page)->send();
        }
    }

    private function getIndentifiers(): array
    {
        $u = new User();
        return [
            "bID" => $this->bID,
            "uID" => $u->getUserID()
        ];
    }

    private function getDb(): Connection
    {
        if ($this->dbConn == null) {
            $this->dbConn = Database::connection();
        }
        return $this->dbConn;
    }

    private function insertDb(array $data)
    {
        $db = $this->getDb();
        $db->insert($this->btJoinUser, $data);
    }

    private function deleteDb(array $identifier)
    {
        $db = $this->getDb();
        $db->delete($this->btJoinUser, $identifier);
    }

    private function updateDb(array $data, array $identifier)
    {
        $db = $this->getDb();
        $db->update($this->btJoinUser, $data, $identifier);
    }

    private function selectDb(array $identifier)
    {
        $tableExpression = $this->btJoinUser;

        if (empty($identifier)) {
            throw InvalidArgumentException::fromEmptyCriteria();
        }

        $columnList = array();
        $criteria = array();
        $paramValues = array();

        foreach ($identifier as $columnName => $value) {
            $columnList[] = $columnName;
            $criteria[] = $columnName . ' = ?';
            $paramValues[] = $value;
        }

        return $this->getDb()->fetchAll(
            'SELECT * FROM ' . $tableExpression . ' WHERE ' . implode(' AND ', $criteria),
            $paramValues
        );
    }
}