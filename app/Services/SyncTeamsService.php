<?php

namespace App\Services;

use App\Models\Team;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Collection;
use PandaScoreAPI\Objects\TeamDto;

/**
 * Class SyncTeamsService
 */
class SyncTeamsService
{
    /**
     * @param TeamDto[] $teamDtoList
     *
     * @return array
     * @throws Exception
     */
    public function sync(array $teamDtoList): array
    {
        $extIdList = array_keys($teamDtoList);

        $hasTeams = $this->getAllByExtId($extIdList);

        $now = Carbon::now();

        $toInsert = [];

        foreach ($teamDtoList as $teamDto) {
            /** @var Team $hasTeamModel */
            $hasTeamModel = $hasTeams->get($teamDto->id);

            if ($hasTeamModel) {
                $extModifiedTime = new DateTime($teamDto->getData()['modified_at'] ?? '1970-01-01');

                if ($hasTeamModel->updated_at->toDateTime() < $extModifiedTime) {
                    $this->updateTeam($hasTeams->get($teamDto->id), $teamDto, $extModifiedTime);
                }

                continue;
            }

            $toInsert[] = [
                'ext_id'     => $teamDto->id,
                'name'       => $teamDto->name,
                'image'      => $teamDto->image_url,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (count($toInsert) > 0) {
            $this->insertNew($toInsert);
        }

        return $this->getAllByExtId($extIdList)->pluck('id', 'ext_id')->toArray();
    }


    /**
     * @param array $extIdList
     *
     * @return Team[]|Collection
     */
    private function getAllByExtId(array $extIdList)
    {
        return Team::query()
            ->whereIn('ext_id', $extIdList)
            ->get()
            ->mapWithKeys(function (Team $itm) {
                return [$itm->ext_id => $itm];
            });
    }

    /**
     * @param Team     $team
     * @param TeamDto  $dto
     * @param DateTime $extModifiedTime
     */
    private function updateTeam(Team $team, TeamDto $dto, DateTime $extModifiedTime): void
    {
        $team->name       = $dto->name;
        $team->image      = $dto->image_url;
        $team->image      = $dto->image_url;
        $team->updated_at = $extModifiedTime;
        $team->save();
    }

    /**
     * @param array $toInsert
     */
    private function insertNew(array $toInsert)
    {
        Team::query()->insert($toInsert);
    }
}