<?php

declare(strict_types=1);

namespace Quantum\Hub;

use DateTime;
use Exception;
use Platine\Config\Config;
use Platine\Http\Client\HttpClient;
use Platine\Http\ServerRequest;
use Platine\Http\ServerRequestInterface;
use Platine\Stdlib\Helper\Arr;
use Platine\Stdlib\Helper\Json;
use Platine\Stdlib\Helper\Str;
use Quantum\Hub\Entity\Commit;
use Quantum\Hub\Entity\Repository;
use Quantum\Hub\Entity\Sender;

/**
 * @class WebHook
 * @package Quantum\Hub
 * @template T
 */
class WebHook
{
    /**
     * The request instance to use
     * @var ServerRequestInterface
     */
    protected ServerRequestInterface $request;

    /**
     * The payload data
     * @var array<string, mixed>
     */
    protected array $payload = [];

    /**
     * Create new instance
     * @param Config<T> $config
     * @param ServerRequestInterface|null $request
     */
    public function __construct(
        protected Config $config,
        ?ServerRequestInterface $request = null,
    ) {
        if ($request === null) {
            $this->request = ServerRequest::createFromGlobals();
        }
    }


    /**
     * Handle the request
     * @return void
     */
    public function handle(): void
    {
        $this->processPayload();

        $repository = $this->getRepository();
        $commit = $this->getCommit();
        $disabledRepos = $this->getDisabledRepositories();
        if (
            $this->getEvent() !== 'push' ||
            $commit === null ||
            in_array(
                $repository->getFullName(),
                $disabledRepos
            )
        ) {
            return;
        }

        // Now send the message
        $this->sendMessage($commit, $repository);
    }

    /**
     * Return the commit branch
     * @return string
     */
    public function getBranch(): string
    {
        $value = $this->getPayloadValue('ref');
        return Arr::last(explode('/', $value));
    }

    /**
     * Return the GitHub Event. Currently only push is supported
     * @return string
     */
    public function getEvent(): string
    {
        return $this->request->getHeaderLine('X-GitHub-Event');
    }

    /**
     * Return the value of the payload
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getPayloadValue(
        string $name,
        mixed $default = null
    ): mixed {
        return Arr::get(
            $this->payload,
            $name,
            $default
        );
    }

    /**
     * Return the repository info
     * @return Repository
     */
    public function getRepository(): Repository
    {
        $data = $this->getPayloadValue('repository', []);

        return new Repository($data);
    }

     /**
     * Return the sender (user who make push) info
     * @return Sender
     */
    public function getSender(): Sender
    {
        $data = $this->getPayloadValue('sender', []);

        return new Sender($data);
    }

    /**
     * Return the commit information if have
     * @return Commit
     */
    public function getCommit(): ?Commit
    {
        $data = $this->getPayloadValue('commits', []);
        if (count($data) === 0) {
            return null;
        }

        return new Commit($data[0]);
    }

    /**
     * Get message content
     * @param Commit $commit
     * @param Repository $repository
     * @return string
     */
    public function getMessageContent(
        Commit $commit,
        Repository $repository
    ): string {
        $sender = $this->getSender();

        $text = sprintf(
            "<a href = \"%s\"><b>@%s</b></a> just push to repository <a href = \"%s\"><b>%s</b></a>%s%s",
            $sender->getHtmlUrl(),
            $sender->getLogin(),
            $repository->getHtmlUrl(),
            $repository->getFullName(),
            PHP_EOL,
            PHP_EOL
        );

        $date = new DateTime($commit->getTimestamp());

        $text .= $this->buildMessageRow('Description', $commit->getMessage(), 2);
        $text .= $this->buildMessageRow('Date', $date->format('Y-m-d H:i:s'));
        $text .= $this->buildMessageRow('Branch/Tag', $this->getBranch());
        $text .= $this->buildMessageRow(
            'Commit ID',
            sprintf(
                '<a href = "%s"><b>%s</b></a>',
                $commit->getUrl(),
                $commit->getId()
            )
        );

        $text .= $this->getCommitFilesChangeMessage($commit->getAdded(), 'added');
        $text .= $this->getCommitFilesChangeMessage($commit->getModified(), 'modified');
        $text .= $this->getCommitFilesChangeMessage($commit->getRemoved(), 'deleted');


        return $text;
    }

    /**
     * Return the list of repository that is disabled
     * @return array<string>
     */
    protected function getDisabledRepositories(): array
    {
        $params = $this->request->getQueryParams();
        $repositories = $params['disable_repos'] ?? '';
        if (empty($repositories)) {
            return [];
        }

        return explode(',', $repositories);
    }

    /**
     * Process the payload
     * @return void
     */
    protected function processPayload(): void
    {
        $body = $this->request->getBody()->getContents();
        if (!empty($body)) {
            $payload = Json::decode($body, true);

            $this->payload = $payload;
        }
    }

    /**
     * Return the commit message files changes (added/removed/modified)
     * @param array<string> $files
     * @param string $type
     * @return string
     */
    protected function getCommitFilesChangeMessage(array $files, string $type): string
    {
        if (count($files) === 0) {
            return '';
        }

        // Use double quote in order to format EOL
        $text = sprintf("%s<b>Files %s:</b>%s", PHP_EOL, $type, PHP_EOL);
        $text .= sprintf("- %s %s", implode(PHP_EOL . '- ', $files), PHP_EOL);

        return $text;
    }

    /**
     * Build the message row
     * @param string $label
     * @param string $value
     * @param int $eolCount
     * @return string
     */
    protected function buildMessageRow(
        string $label,
        string $value,
        int $eolCount = 1
    ): string {
        return sprintf(
            "<b>%s:</b> %s%s",
            $label,
            $value,
            Str::repeat(PHP_EOL, $eolCount)
        );
    }

    /**
     * Return the chat id and bot token to be used
     * @param Repository $repository
     * @return array{chat:string, token:string}|null
     */
    protected function getConfigInfo(Repository $repository): ?array
    {
        $organization = $repository->getOrganization();
        $repoName = $repository->getName();
        $configKey = 'config.organization.%s.%s';

        // Check for one repository configuration
        $repoConfig = $this->config->get(
            sprintf($configKey, $organization, $repoName),
            []
        );
        if (count($repoConfig) === 2) {
            return [
                'chat' => $repoConfig[0],
                'token' => $repoConfig[1],
            ];
        }

        // Default
        $allConfig = $this->config->get(
            sprintf($configKey, $organization, '*'),
            []
        );
        if (count($allConfig) === 2) {
            return [
                'chat' => $allConfig[0],
                'token' => $allConfig[1],
            ];
        }

        return null;
    }

    /**
     * Send the message
     * @param Commit $commit
     * @param Repository $repository
     * @return bool
     */
    protected function sendMessage(
        Commit $commit,
        Repository $repository
    ): bool {
        $config = $this->getConfigInfo($repository);
        if ($config === null) {
            return false;
        }

        $message = $this->getMessageContent($commit, $repository);
        if (empty($message)) {
            return false;
        }

        $url = sprintf('https://api.telegram.org/bot%s/sendMessage', $config['token']);
        $params = [
            'chat_id' => $config['chat'],
            'parse_mode' => 'HTML',
            'text' => $message,
        ];

        $client = new HttpClient($url);
        $client->json()
               ->verifySslCertificate(false);
        try {
            $response = $client->post('', $params);

            if ($response->isError()) {
                error_log($response->getError());

                return false;
            }

            return true;
        } catch (Exception $ex) {
            error_log($ex->getMessage());
            return false;
        }
    }
}
