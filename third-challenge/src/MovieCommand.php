<?php


namespace Acme;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use GuzzleHttp\ClientInterface;

class MovieCommand extends Command
{
    private $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
        parent::__construct();
    }

    public function configure()
    {
        $this->setName('show')
            ->setDescription('get the movie information')
            ->addArgument('name', InputArgument::REQUIRED, 'name of the movie')
            ->addOption('fullPlot', null, InputOption::VALUE_NONE, 'Include the plot of the movie');
    }
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $title = $input->getArgument('name');
        $plot = 'short';
        $plotOption = $input->getOption('fullPlot');
        if($plotOption != null) $plot = 'full';

        $return = $this->getMovieData($title, $plot);

        $dataArray = json_decode($return);

        $rows = $this->createRows($dataArray);

        $title = $dataArray->{'Title'};
        $year = $dataArray->{'Year'};

        $table = new Table($output);

        $table->setHeaders([$title, $year])
            ->setRows($rows);
        $table->render();
    }

    private function getMovieData($title, $plot){
        $url = 'http://www.omdbapi.com/?apikey=d344f0d6&t='.$title.'&plot='.$plot;
        $response = $this->client->get($url)->getBody()->getContents();

        return $response;
    }

    private function createRows($dataArray)
    {
        $rows = [];
        foreach ($dataArray as $key => $value) {
            if(is_string($key) && is_string($value))
                array_push($rows, [$key, $value]);
        }
        return $rows;
    }
}