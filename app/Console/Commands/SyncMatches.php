<?php

namespace App\Console\Commands;

use App\Services\SyncLeaguesService;
use App\Services\SyncTeamsService;
use Exception;
use Illuminate\Console\Command;
use PandaScoreAPI\Objects\LeagueDto;
use PandaScoreAPI\Objects\TeamDto;
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

    /**
     * Create a new command instance.
     *
     * @param PandaScoreAPI    $api
     * @param SyncTeamsService $syncTeams
     */
    public function __construct(PandaScoreAPI $api,
                                SyncTeamsService $syncTeams,
                                SyncLeaguesService $syncLeagues)
    {
        parent::__construct();

        $this->api         = $api;
        $this->syncTeams   = $syncTeams;
        $this->syncLeagues = $syncLeagues;
    }


    /**
     * @throws Exception
     */
    public function handle()
    {
        $matches = $this->api->matches->getUpcomingMatches();

        $teamDtoList   = [];
        $leagueDtoList = [];

        foreach ($matches as $matchDto) {
            if ($matchDto->opponents) {
                foreach ($matchDto->opponents as $opponentDto) {
                    $teamDtoList[$opponentDto->opponent->id] = $opponentDto->opponent;
                }
            }

            $leagueDtoList[$matchDto->league->id] = $matchDto->league;
        }

        $teamSync   = $this->syncTeams($teamDtoList);
        $leagueSync = $this->syncLeagues($leagueDtoList);

        // todo update matches structure, add opponents id

        // todo sync matches


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
