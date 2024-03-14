<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Symfony Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2023-2024 scorpion3dd
 */

declare(strict_types=1);

namespace App\CQRS\Query\FindUserByEmail;

use App\CQRS\DTO\UserDTO;
use App\Entity\User;
use App\Helper\ApplicationGlobals;
use App\Service\UserServiceInterface;
use App\Util\ConsoleOutputTrait;
use App\Util\LoggerTrait;
use Exception;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Class FindUserByEmailQueryHandler
 * @package App\CQRS\Query\FindUserByEmail
 */
#[AsMessageHandler]
class FindUserByEmailQueryHandler implements QueryHandlerInterface
{
    use LoggerTrait;
    use ConsoleOutputTrait;

    /**
     * @param UserServiceInterface $userService
     * @param ApplicationGlobals $appGlobals
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly UserServiceInterface $userService,
        ApplicationGlobals $appGlobals,
        LoggerInterface $logger
    ) {
        $this->appGlobals = $appGlobals;
        $this->logger = $logger;
        $this->input = new ArgvInput();
        $this->output = new ConsoleOutput();
        $this->buildIo($this->input, $this->output);
        $this->debugConstruct(self::class);
    }

    /**
     * @psalm-suppress DeprecatedConstant
     * @param FindUserByEmailQuery $query
     *
     * @return UserDTO
     * @throws Exception
     */
    public function __invoke(FindUserByEmailQuery $query): UserDTO
    {
        $this->debugFunction(self::class, 'invoke');
        $name = self::class . ' invoke';
        $this->debugMessage(self::class, 'start');
        $this->echo($name . ' - started', Logger::NOTICE);

        $this->debugMessage(self::class, 'Find user');
        $this->debugParameters(self::class, ['email' => $query->email]);
        $this->echo('Find user by email = ' . $query->email, Logger::DEBUG);

        /** @var User|null $user */
        $user = $this->userService->findOneByField('email', $query->email);
        if (! $user) {
            throw new Exception('User not found');
        }

        $userDTO = UserDTO::fromEntity($user);
        $this->debugParameters(self::class, ['UserDTO id' => $userDTO->id]);
        $this->echo('UserDTO id = ' . $userDTO->id, Logger::DEBUG);

        $this->debugMessage(self::class, 'finish');
        $this->echo($name . ' - finished', Logger::NOTICE);

        return $userDTO;
    }
}
