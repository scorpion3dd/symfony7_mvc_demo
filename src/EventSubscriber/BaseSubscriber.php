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

namespace App\EventSubscriber;

use App\Entity\Permission;
use App\Entity\Role;
use App\Entity\User;
use App\Enum\Roles;
use App\Util\LoggerTrait;
use Carbon\Carbon;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BaseSubscriber
 * @package App\EventSubscriber
 */
abstract class BaseSubscriber
{
    use LoggerTrait;

    public const MIME_JSON = 'application/json';
    public const MIME_LD_JSON = 'application/ld+json';
    public const MIME_TEXT_HTML = 'text/html';
    public const DOCTYPE = '<!DOCTYPE';
    public const HYDRA_DESCRIPTION = 'hydra:description';

    /** @var array $entities */
    protected array $entities = [];

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param string|null $contentType
     * @param string|null $acceptHeader
     *
     * @return bool
     */
    protected function isTextHtml(?string $contentType = '', ?string $acceptHeader = ''): bool
    {
        return (! empty($contentType) && false !== strpos(strtoupper($contentType), strtoupper(self::MIME_TEXT_HTML)))
            || (! empty($acceptHeader) && false !== strpos(strtoupper($acceptHeader), strtoupper(self::MIME_TEXT_HTML)));
    }

    /**
     * @param string|null $acceptHeader
     *
     * @return bool
     */
    protected function isJson(?string $acceptHeader = ''): bool
    {
        return self::MIME_JSON === $acceptHeader || self::MIME_LD_JSON === $acceptHeader;
    }

    /**
     * @param string $content
     *
     * @return string
     */
    protected function buildContentText(string $content): string
    {
        if (false !== strpos(strtoupper($content), strtoupper(self::DOCTYPE))) {
            $content = 'html';
        } else {
            $strBegin = '<!-- ';
            $strEnd = ' -->';
            $pos1 = strpos($content, $strBegin) ?: 0;
            $pos2 = strpos($content, $strEnd) ?: 0;
            $content = substr($content, $pos1 + strlen($strBegin), $pos2 - strlen($strEnd));
        }

        return $content;
    }

    /**
     * @param int $statusCode
     * @param Response $response
     * @param Request $request
     * @param string $content
     *
     * @return array
     */
    protected function buildContentJson(int $statusCode, Response $response, Request $request, string $content = ''): array
    {
        $result = [
            'content' => $content,
            'response' => $response,
        ];
        $json = json_decode($content, true);
        if ($statusCode > 401 || ($statusCode == 400 && isset($json[self::HYDRA_DESCRIPTION]))) {
            if ($statusCode !== 404) {
                $response->setStatusCode(400);
            }
            if ($statusCode !== 422) {
                $description = "";
                if (isset($json[self::HYDRA_DESCRIPTION])) {
                    $description = $json[self::HYDRA_DESCRIPTION];
                }
                if (false !== strpos(strtoupper($description), strtoupper('Unable to generate an IRI for the item of type'))) {
                    $description = 'Bad request';
                }
                if ($description == '' && false !== strpos(strtoupper($content), strtoupper('No route found'))) {
                    $description = 'No route found for ' . $request->server->get('REQUEST_URI');
                }
                if ($description == "" && isset($json['message'])) {
                    $description = $json['message'];
                }
                $content = json_encode(['error' => $description]);
                $result['content'] = $content;
                $response->setContent((string) $content);
            }
            $result['response'] = $response;
        }

        return $result;
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @return void
     */
    public function setEntities(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        $this->entities[] = $entity;
    }

    /**
     * @param object $entity
     *
     * @return object
     */
    protected function entityPrePersist(object $entity): object
    {
        $class = get_class($entity);
        $this->debugParameters(self::class, ['class' => $class]);
        switch ($class) {
            case User::class:
                /** @var User $entity */
                $entity->setSlug($entity->buildSlug());
                $entity->setRoles([Roles::ROLE_USER]);
                $entity->setCreatedAt(Carbon::now());
                break;
            case Permission::class:
            case Role::class:
                /** @var Role|Permission $entity */
                $entity->setDateCreated(Carbon::now());
                break;
        }

        return $entity;
    }

    /**
     * @param object $entity
     *
     * @return object
     */
    protected function entityPreUpdate(object $entity): object
    {
        $class = get_class($entity);
        $this->debugParameters(self::class, ['class' => $class]);
        switch ($class) {
            case User::class:
                /** @var User $entity */
                $entity->setSlug($entity->buildSlug());
                $entity->setUpdatedAt(Carbon::now());
                break;
            case Permission::class:
            case Role::class:
                break;
        }

        return $entity;
    }
}
