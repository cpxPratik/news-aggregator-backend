<?php

namespace App\Console\Commands;

use App\DataSources\GuardianSource;
use App\DataSources\NewsApiSource;
use App\DataSources\NewsSource;
use App\DataSources\NewYorkTimesSource;
use App\Models\Source;
use App\Services\ArticleImporter;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;

#[Signature('app:fetch-news')]
#[Description('Fetch the latest articles from the data sources')]
class FetchNews extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(ArticleImporter $articleImporter): int
    {
        $newsDataAdapters = [new NewsApiSource, new GuardianSource, new NewYorkTimesSource];

        $total = 0;
        $failed = false;

        foreach ($newsDataAdapters as $adapter) {
            $count = $this->importFrom($articleImporter, $adapter);

            if ($count === null) {
                $failed = true;
            } else {
                $total += $count;
            }
        }

        $this->info("Total {$total} articles fetched from all sources.");

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function importFrom(ArticleImporter $importer, NewsSource $adapter): ?int
    {
        $source = Source::where('slug', $adapter->slug())->first();
        if (! $source) {
            $this->warn("{$adapter->slug()} not found in database.");

            return 0;
        }

        try {
            $count = $importer->import($source, $adapter->fetch());
        } catch (ConnectionException $e) {
            $this->error("{$source->slug}: could not connect. {$e->getMessage()}.");

            return null;
        }

        $this->info("{$source->slug}: {$count} articles fetched.");

        return $count;
    }
}
