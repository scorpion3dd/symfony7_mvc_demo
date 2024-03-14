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

namespace App\CQRS\Command\CreateUser;

use App\Factory\UserFactory;
use App\Helper\ApplicationGlobals;
use App\Service\UserServiceInterface;
use App\Util\ConsoleOutputTrait;
use App\Util\LoggerTrait;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Class CreateUserCommandHandler
 * @package App\CQRS\Command\CreateUser
 */
#[AsMessageHandler]
class CreateUserCommandHandler implements CommandHandlerInterface
{
    use LoggerTrait;
    use ConsoleOutputTrait;

    /**
     * @param UserServiceInterface $userService
     * @param UserFactory $userFactory
     * @param ApplicationGlobals $appGlobals
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly UserFactory $userFactory,
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
     * @return int UserId
     */
    public function __invoke(CreateUserCommand $createUserCommand): int
    {
        $this->debugFunction(self::class, 'invoke');
        $name = self::class . ' invoke';
        $this->debugMessage(self::class, 'start');
        $this->echo($name . ' - started', Logger::NOTICE);

        $user = $this->userFactory->create(
            $createUserCommand->email,
            $createUserCommand->genderId,
            $createUserCommand->username,
            $createUserCommand->fullName,
            $createUserCommand->description,
            $createUserCommand->statusId,
            $createUserCommand->accessId,
            $createUserCommand->dateBirthday,
            $createUserCommand->createdAt,
            $createUserCommand->updatedAt,
        );
        $this->userService->save($user, true);

        $userId = $user->getId() ?? 0;
        $this->debugMessage(self::class, 'user saved');
        $this->debugParameters(self::class, ['userId' => $userId]);
        $this->echo('user saved, user-id = ' . $userId, Logger::DEBUG);

        $this->debugMessage(self::class, 'finish');
        $this->echo($name . ' - finished', Logger::NOTICE);

        return $userId;
    }
}
