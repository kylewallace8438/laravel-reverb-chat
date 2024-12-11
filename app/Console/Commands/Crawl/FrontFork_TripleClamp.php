<?php

namespace App\Console\Commands\Crawl;

use App\Models\Brand;
use App\Models\Crawl\KTMCategoryScrape;
use App\Models\Crawl\KTMPartScrape;
use App\Models\Crawl\ScrapeObserver;
use App\Models\CrawlCategoryBrand;
use Spatie\Crawler\Crawler;

use Illuminate\Console\Command;

class FrontFork_TripleClamp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:front-fork_-triple-clamp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $domain = 'https://www.bike-parts-ktm.com';
        $url = $domain . '/ktm-motorcycle/390_MOTO/2016/DUKE/390-DUKE-WHITE-ABS/FRONT-FORK--TRIPLE-CLAMP/655/0/0/655';
        $cateogry_url = '/ktm-motorcycle/390_MOTO/2016/DUKE/390-DUKE-WHITE-ABS/655';
        $ktmCategoryObserver = new KTMCategoryScrape();
        Crawler::create()
            ->setMaximumDepth(0)
            ->setTotalCrawlLimit(1)
            ->setCrawlObserver($ktmCategoryObserver)
            ->startCrawling($domain . $cateogry_url);
        $ktm390CategoryCategories = $ktmCategoryObserver->getContent();
        $data = [];
        if (is_array($ktm390CategoryCategories)) {
            foreach ($ktm390CategoryCategories as $c) {
                $string = substr($c['name'], strpos($c['name'], 'for'), strlen($c['name']));
                $c['name'] = str_replace($string, '', $c['name']);
                $data[] = [
                    'brand_id' => Brand::where('slug', 'ktm')->first()->id,
                    'name' => $c['name'],
                    'url' => $c['url']
                ];
            }
        }
        CrawlCategoryBrand::create($data);
        // $scrapeObserver = new KTMPartScrape();
        // Crawler::create()
        //     ->setMaximumDepth(0)
        //     ->setTotalCrawlLimit(1)
        //     ->setCrawlObserver($scrapeObserver)
        //     ->startCrawling($url);
        // dd($scrapeObserver->getContent());
    }
}
