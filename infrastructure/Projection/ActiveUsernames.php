<?php

namespace Infrastructure\Projection;

use BoundedContext\Projection\AbstractProjection;
use BoundedContext\ValueObject\Uuid;
use Domain\Test\ValueObject\Username;

class ActiveUsernames extends AbstractProjection implements \Domain\Test\Projection\ActiveUsernames\Projection
{
    private $active_usernames;
    private $aggregate_index;

    public function __construct()
    {
        parent::__construct();

        $this->active_usernames = [];
        $this->aggregate_index = [];
    }

    public function reset()
    {
        parent::reset();

        $this->active_usernames = [];
        $this->aggregate_index = [];
    }

    public function exists(Username $username)
    {
        return array_key_exists($username->toString(), $this->active_usernames);
    }

    public function add(Uuid $id, Username $username)
    {
        if($this->exists($username))
        {
            throw new \Exception("The username [$username->toString()] is already active.");
        }

        $this->aggregate_index[$id->toString()] = $username->toString();
        $this->active_usernames[$username->toString()] = 1;
    }

    public function remove(Uuid $id)
    {
        $username = $this->aggregate_index[$id->toString()];

        if(!$this->exists(new Username($username)))
        {
            throw new \Exception("The username [$username] is not active.");
        }

        unset($this->active_usernames[$username]);
        unset($this->aggregate_index[$id->toString()]);
    }

    public function replace(Uuid $id, Username $old_username, Username $new_username)
    {
        $this->remove($id);
        $this->add($id, $new_username);
    }
}
