<?php

namespace App\Services;


use App\Models\Match;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Collection;
use PandaScoreAPI\Objects\MatchDto;

/**
 * Class SyncMatchesService
 *
 * @package App\Services
 */
class SyncMatchesService
{
    /**
     * @param MatchDto[] $matchDtoList
     * @param array      $syncTeamDict
     * @param array      $syncLeagueDict
     *
     * @throws Exception
     */
    public function sync(array $matchDtoList, array $syncTeamDict, array $syncLeagueDict): void
    {
        $extIdList = array_keys($matchDtoList);

        $hasMatches = $this->getAllByExtId($extIdList);

        $now = Carbon::now();

        $toInsert       = [];
        $syncMatchTeams = [];

        foreach ($matchDtoList as $matchDto) {
            /** @var Match $hasMatchModel */
            $hasMatchModel = $hasMatches->get($matchDto->id);

            $syncMatchTeams[$matchDto->id] = $this->getAttachedTeam($matchDto->opponents ?? [], $syncTeamDict);

            if ($hasMatchModel) {
                $local_league_id = $syncLeagueDict[$matchDto->league_id];
                $this->updateMatch($hasMatchModel, $matchDto, $local_league_id);

                continue;
            }

            $toInsert[] = [
                'ext_id'     => $matchDto->id,
                'begin_at'   => new DateTime($matchDto->begin_at),
                'begin_at'   => new DateTime($matchDto->begin_at),
                'name'       => $matchDto->name,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (count($toInsert) > 0) {
            $this->insertNew($toInsert);
        }

        $this->syncMathTeams($syncMatchTeams);
    }

    /**
     * Get local team id for sync with match
     *
     * @param array $opponentsList
     * @param array $syncTeamDict
     *
     * @return array
     */
    private function getAttachedTeam(?array $opponentsList, array $syncTeamDict): ?array
    {
        if (is_null($opponentsList)) {
            return [];
        }

        $res = [];

        foreach ($opponentsList as $opponentDto) {
            $ext_team_id = $opponentDto->opponent->id;

            if (!isset($syncTeamDict[$ext_team_id])) {
                // todo make exception or log message
                continue;
            }

            $res[] = $syncTeamDict[$ext_team_id];
        }

        return $res;
    }

    /**
     * @param array $extIdList
     *
     * @return Match[]|Collection
     */
    private function getAllByExtId(array $extIdList)
    {
        return Match::query()
            ->whereIn('ext_id', $extIdList)
            ->get()
            ->mapWithKeys(function (Match $itm) {
                return [$itm->ext_id => $itm];
            });
    }

    /**
     * @param Match    $matchModel
     * @param MatchDto $dto
     * @param int|null $local_league_id
     *
     * @throws Exception
     */
    private function updateMatch(Match $matchModel, MatchDto $dto, ?int $local_league_id): void
    {
        $extModifiedTime = new DateTime($dto->modified_at ?? '1970-01-01');

        if ($matchModel->updated_at->toDateTime() >= $extModifiedTime) {
            return;
        }

        $matchModel->league_id  = $local_league_id;
        $matchModel->name       = $dto->name;
        $matchModel->begin_at   = new DateTime($dto->begin_at);
        $matchModel->updated_at = $extModifiedTime;
        $matchModel->save();
    }

    /**
     * @param array $toInsert
     */
    private function insertNew(array $toInsert): void
    {
        Match::query()->insert($toInsert);
    }

    /**
     * @param array $attachTeams
     */
    private function syncMathTeams(array $attachTeams): void
    {
        $extIdList = array_keys($attachTeams);

        $hasMatches = $this->getAllByExtId($extIdList);

        foreach ($hasMatches as $match_id => $matchModel) {
            $matchModel->teams()->sync($attachTeams[$match_id]);
        }
    }
}