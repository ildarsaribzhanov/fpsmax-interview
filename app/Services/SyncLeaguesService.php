<?php

namespace App\Services;


use App\Models\League;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Support\Collection;
use PandaScoreAPI\Objects\LeagueDto;

/**
 * Class SyncLeaguesService
 *
 * @package App\Services
 */
class SyncLeaguesService
{
    /**
     * @param LeagueDto[] $leaguesList
     *
     * @return array
     * @throws Exception
     */
    public function sync(array $leaguesList): array
    {
        $extIdList = array_keys($leaguesList);

        $hasLeagues = $this->getAllByExtId($extIdList);

        $now = Carbon::now();

        $toInsert = [];

        foreach ($leaguesList as $leagueDto) {
            /** @var League $hasLeagueModel */
            $hasLeagueModel = $hasLeagues->get($leagueDto->id);

            if ($hasLeagueModel) {
                $this->updateLeague($hasLeagueModel, $leagueDto);

                continue;
            }

            $toInsert[] = [
                'ext_id'     => $leagueDto->id,
                'name'       => $leagueDto->name,
                'url'        => $leagueDto->url,
                'image'      => $leagueDto->image_url,
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
     * @return LeagueDto[]|Collection
     */
    private function getAllByExtId(array $extIdList)
    {
        return League::query()
            ->whereIn('ext_id', $extIdList)
            ->get()
            ->mapWithKeys(function (League $itm) {
                return [$itm->ext_id => $itm];
            });
    }

    /**
     * @param League    $leagueModel
     * @param LeagueDto $dto
     *
     * @throws Exception
     */
    private function updateLeague(League $leagueModel, LeagueDto $dto): void
    {
        $extModifiedTime = new DateTime($leagueDto->modified_at ?? '1970-01-01');

        if ($leagueModel->updated_at->toDateTime() >= $extModifiedTime) {
            return;
        }

        $leagueModel->name       = $dto->name;
        $leagueModel->url        = $dto->url;
        $leagueModel->image      = $dto->image_url;
        $leagueModel->updated_at = $extModifiedTime;
        $leagueModel->save();
    }

    /**
     * @param array $toInsert
     */
    public function insertNew(array $toInsert): void
    {
        League::query()->insert($toInsert);
    }
}