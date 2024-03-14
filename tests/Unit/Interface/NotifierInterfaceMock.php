<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Tests\Unit\Interface;

use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Component\Notifier\NotifierInterface;

/**
 * Test Interface for the Notifier system.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface NotifierInterfaceMock extends NotifierInterface
{
    public function send(Notification $notification, RecipientInterface ...$recipients): void;

    public function getAdminRecipients(): array;
}
