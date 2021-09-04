<?php

namespace App\Authentication;

use React\MySQL\ConnectionInterface;
use React\MySQL\QueryResult;
use React\Promise\PromiseInterface;
use function React\Promise\reject;
use function React\Promise\resolve;

final class Storage
{
    private $connection;
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function create(string $email, string $password): PromiseInterface
    {
        return $this->emailIsNotTaken($email)
            ->then(function () use ($email, $password) {
                return $this->connection
                    ->query(
                        'INSERT INTO users (email, password) VALUES (?, ?)',
                        [$email, $password]
                    );
            });
    }

    public function emailIsNotTaken(string $email): PromiseInterface
    {
        return $this->connection
            ->query(
                'SELECT 1 from users where email = ?',
                [$email]
            )
            ->then(function (QueryResult $result) {
                return empty($result->resultRows) ? resolve() : reject(new UserAlreadyExists());
            });
    }

    public function findByEmail(string $email): PromiseInterface
    {
        return $this->connection
            ->query(
                'SELECT id, email, password from users where email = ?',
                [$email]
            )
            ->then(function (QueryResult $result) {
                if (empty($result->resultRows)) {
                    throw new UserNotFoundException();
                }

                $row = $result->resultRows[0];

                return new User((int)$row['id'], $row['email'], $row['password']);
            });
    }
}
