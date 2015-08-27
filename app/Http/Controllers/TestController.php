<?php

namespace App\Http\Controllers;

use BoundedContext\Collection\Collection;
use BoundedContext\Log\Item;
use BoundedContext\ValueObject\DateTime;
use BoundedContext\ValueObject\Version;
use Domain\Test\Aggregate\User\Event\Created;
use Domain\Test\ValueObject\EmailAddress;
use Domain\Test\ValueObject\EncryptedPassword;
use Domain\Test\ValueObject\Password;
use Domain\Test\ValueObject\Username;
use Domain\Test\Aggregate\User\Command;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Http\Request;

use BoundedContext\ValueObject\Uuid;

class TestController extends Controller
{
    protected $bus;
    protected $app;

    public function __construct(Dispatcher $bus, Application $app)
    {
        $this->bus = $bus;
        $this->app = $app;
    }

    public function create(Request $request)
    {
        /*$log->append_collection(new Collection([
            new Created(
                new Uuid('b98540d7-c3f9-4af3-8d77-e46662fcb3f6'),
                new Username('lyonscf'),
                new EmailAddress('colin@tercet.io'),
                new EncryptedPassword('lol')
            ),
            new Created(
                new Uuid('b98540d7-c3f9-4af3-8d77-e46662fcb3f7'),
                new Username('lyonscf'),
                new EmailAddress('colin@tercet.io'),
                new EncryptedPassword('lol')
            ),
        ]));

        $last_id = Uuid::null();

        echo "Streaming from: ";
        var_dump($last_id->serialize());

        $stream = $log->get_stream($last_id);

        while($stream->has_next())
        {
            $item = $stream->next();

            $last_id = $item->id();

            echo "Found: ";
            var_dump($last_id->serialize());
        }

        echo "Executing new Commands...<br>";

        $log->append_collection(new Collection([
            new Created(
                new Uuid('b98540d7-c3f9-4af3-8d77-e46662fcb3f8'),
                new Username('lyonscf'),
                new EmailAddress('colin@tercet.io'),
                new EncryptedPassword('lol')
            ),
            new Created(
                new Uuid('b98540d7-c3f9-4af3-8d77-e46662fcb3f9'),
                new Username('lyonscf'),
                new EmailAddress('colin@tercet.io'),
                new EncryptedPassword('lol')
            ),
        ]));

        echo "State of Log";
        var_dump($this->app->make('BoundedContext\Contracts\Log')->query()->get());

        echo "Streaming from: ";
        var_dump($last_id->serialize());

        $stream = $log->get_stream($last_id);

        echo "Found: ";
        while($stream->has_next())
        {
            $item = $stream->next();

            $last_id = $item->id();

            var_dump($last_id->serialize());
        }

        echo "Streaming from: ";
        var_dump($last_id->serialize());

        $stream = $log->get_stream($last_id);

        echo "Found: ";
        while($stream->has_next())
        {
            $item = $stream->next();

            $last_id = $item->id();

            var_dump($last_id->serialize());
        }

        dd('lol');*/

        $log = $this->app->make('BoundedContext\Contracts\Log');
        $log->reset();

        $aggregate_collections = $this->app->make('BoundedContext\Projector\AggregateCollections');
        $aggregate_collections->projection()->reset();

        $this->bus->dispatch(new Command\Create(
            new Uuid('b98540d7-c3f9-4af3-8d77-e46662fcb3f6'),
            new Username('bphilson'),
            new EmailAddress('bphilson@gmail.com'),
            new Password('roflcopter')
        ));

        $this->bus->dispatch(new Command\ChangeUsername(
            new Uuid('b98540d7-c3f9-4af3-8d77-e46662fcb3f6'),
            new Username('lyonscf2')
        ));

        $this->bus->dispatch(new Command\ChangeUsername(
            new Uuid('b98540d7-c3f9-4af3-8d77-e46662fcb3f6'),
            new Username('lyonscf3')
        ));

        $this->bus->dispatch(new Command\Delete(
            new Uuid('b98540d7-c3f9-4af3-8d77-e46662fcb3f6')
        ));

        echo "<pre>";

        $aggregate_collections = $this->app->make('BoundedContext\Projector\AggregateCollections');
        echo "\nAggregateCollections: " .
            $aggregate_collections->projection()->version()->serialize() .
            " of " .
            $aggregate_collections->projection()->count()->serialize()
        ;

        $active_usernames = $this->app->make('Domain\Test\Projection\ActiveUsernames\Projector');
        echo "\nActiveUsernames: " .
            $active_usernames->projection()->version()->serialize() .
            " of " .
            $active_usernames->projection()->count()->serialize()
        ;

        $active_emails = $this->app->make('Domain\Test\Projection\ActiveEmails\Projector');
        echo "\nActiveEmails: " .
            $active_emails->projection()->version()->serialize() .
            " of " .
            $active_emails->projection()->count()->serialize()
        ;
        echo "</pre>";

        //$projector = $this->app->make('App\Projections\Users\Projector');
        //$projector->play();
        //dd($projector);
        
        dd($this->app->make('BoundedContext\Contracts\Log'));
    }
}