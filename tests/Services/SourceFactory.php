<?php

declare(strict_types=1);

namespace SmartAssert\SourcesClient\Tests\Services;

use SmartAssert\SourcesClient\Model\OriginSourceInterface;
use SmartAssert\SourcesClient\Model\RunSource;
use SmartAssert\SourcesClient\Model\SourceInterface;
use Symfony\Component\Uid\Ulid;

class SourceFactory
{
    public function __construct(
        private readonly DataRepository $dataRepository,
    ) {
    }

    /**
     * @param string[]         $parameters
     * @param non-empty-string $state
     */
    public function createRunSourceFromOriginSource(
        OriginSourceInterface $parent,
        array $parameters,
        string $state,
        ?string $failureReason = null,
        ?string $failureMessage = null,
    ): RunSource {
        $sourceId = (string) new Ulid();
        if ('' === $sourceId) {
            throw new \RuntimeException('Empty source id generated');
        }

        $runSource = new RunSource(
            $sourceId,
            $parent->getUserId(),
            $parameters,
            $state,
            $parent->getId(),
            $failureReason,
            $failureMessage,
        );

        $this->persistSource($runSource);
        $this->persistRunSource($runSource);

        return $runSource;
    }

    /**
     * @param non-empty-string $userId
     * @param string[]         $parameters
     * @param non-empty-string $state
     */
    public function createRunSourceWithoutParent(
        string $userId,
        array $parameters,
        string $state,
        ?string $failureReason = null,
        ?string $failureMessage = null,
    ): RunSource {
        $sourceId = (string) new Ulid();
        if ('' === $sourceId) {
            throw new \RuntimeException('Empty source id generated');
        }

        $runSource = new RunSource($sourceId, $userId, $parameters, $state, null, $failureReason, $failureMessage);

        $this->persistSource($runSource);
        $this->persistRunSource($runSource);

        return $runSource;
    }

    public function persistRunSource(RunSource $source): void
    {
        $statement = $this->dataRepository->getConnection()->prepare(
            'INSERT INTO run_source(
                       id, parent_id, parameters, state, failure_reason, failure_message
                   ) VALUES(
                       :id, :parent_id, :parameters, :state, :failure_reason, :failure_message
                   )'
        );

        $statement->execute([
            'id' => $source->getId(),
            'parent_id' => $source->getParent(),
            'parameters' => json_encode($source->getParameters()),
            'state' => $source->getState(),
            'failure_reason' => $source->getFailureReason(),
            'failure_message' => $source->getFailureMessage(),
        ]);
    }

    private function persistSource(SourceInterface $source): void
    {
        $statement = $this->dataRepository->getConnection()->prepare(
            'INSERT INTO source(
                       id, user_id, type
                   ) VALUES(
                       :id, :user_id, :type
                   )'
        );

        $statement->execute([
            'id' => $source->getId(),
            'user_id' => $source->getUserId(),
            'type' => $source->getType(),
        ]);
    }
}
