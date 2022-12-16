<?php

declare(strict_types=1);

namespace genug\Router;

use genug\Environment\EnvironmentInterface;
use genug\Page\{
    Entity as PageEntity,
    EntityNotFound as PageEntityNotFound,
    RepositoryInterface as PageRepositoryInterface,
};
use genug\Request\RequestInterface;
use Psr\Log\LoggerInterface;

use function sprintf;

/**
 *
 * @author David Ringsdorf http://davidringsdorf.de
 * @license MIT License
 */
final class Router implements RouterInterface
{
    public function __construct(
        protected readonly RequestInterface $request,
        protected readonly PageRepositoryInterface $pages,
        protected readonly EnvironmentInterface $environment,
        protected readonly LoggerInterface $logger
    ) {
    }

    public function result(): PageEntity
    {
        try {
            try {
                return $this->pages->fetch($this->request->pageId());
            } catch (PageEntityNotFound $t) {
                $this->logger->debug(sprintf('Requested page "%s" not found.', $this->request->pageId()), ['throwable' => $t]);
                $this->logger->debug(sprintf('Fetch the http-404 page instead.'), ['http404page_id' => (string) $this->environment->http404PageId()]);
                return $this->pages->fetch((string) $this->environment->http404PageId());
            }
        } catch (PageEntityNotFound $t) {
            $this->logger->error('No result.', ['throwable' => $t]);
            throw new RouterError(previous: $t);
        }
    }
}
