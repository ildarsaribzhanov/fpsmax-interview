<?php

namespace App\Console\Commands;

use App\Services\{SyncLeaguesService, SyncMatchesService, SyncTeamsService};
use Exception;
use Illuminate\Console\Command;
use PandaScoreAPI\Objects\{LeagueDto, TeamDto};
use PandaScoreAPI\PandaScoreAPI;

/**
 * Class SyncMatches
 *
 * @package App\Console\Commands
 */
class SyncMatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncMatches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize matches form PandaScope';

    /** @var PandaScoreAPI */
    private $api;

    /** @var SyncTeamsService */
    private $syncTeams;

    /** @var SyncLeaguesService */
    private $syncLeagues;

    /** @var SyncMatchesService */
    private $syncMatches;

    /**
     * Create a new command instance.
     *
     * @param PandaScoreAPI      $api
     * @param SyncTeamsService   $syncTeams
     * @param SyncLeaguesService $syncLeagues
     * @param SyncMatchesService $syncMatches
     */
    public function __construct(PandaScoreAPI $api,
                                SyncTeamsService $syncTeams,
                                SyncLeaguesService $syncLeagues,
                                SyncMatchesService $syncMatches)
    {
        parent::__construct();

        $this->api         = $api;
        $this->syncTeams   = $syncTeams;
        $this->syncLeagues = $syncLeagues;
        $this->syncMatches = $syncMatches;
    }


    /**
     * @throws Exception
     */
    public function handle()
    {
        $matches = $this->api->matches->getUpcomingMatches();

        $teamDtoList   = [];
        $leagueDtoList = [];
        $matchDtoList  = [];

        foreach ($matches as $matchDto) {
            if ($matchDto->opponents) {
                foreach ($matchDto->opponents as $opponentDto) {
                    $teamDtoList[$opponentDto->opponent->id] = $opponentDto->opponent;
                }
            }

            $leagueDtoList[$matchDto->league->id] = $matchDto->league;

            $matchDtoList[$matchDto->id] = $matchDto;
        }

        $teamSync   = $this->syncTeams($teamDtoList);
        $leagueSync = $this->syncLeagues($leagueDtoList);
        $this->syncMatches->sync($matchDtoList, $teamSync, $leagueSync);
    }

    /**
     * @param TeamDto[] $teamList
     *
     * @return array
     * @throws Exception
     */
    private function syncTeams(array $teamList): array
    {
        if (count($teamList) <= 0) {
            return [];
        }

        return $this->syncTeams->sync($teamList);
    }

    /**
     * @param LeagueDto[] $leagueList
     *
     * @return array
     * @throws Exception
     */
    private function syncLeagues(array $leagueList): array
    {
        if (count($leagueList) <= 0) {
            return [];
        }

        return $this->syncLeagues->sync($leagueList);
    }
}
