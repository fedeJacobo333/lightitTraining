<?php


namespace Acme;


use Symfony\Component\Console\Command\Command;
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
        $plotOpcion = $input->getOption('fullPlot');
        if($plotOpcion != null) $plot = 'full';

        $return = $this->download($title, $plot);

        $output->writeln($return);
    }

    private function download($title, $plot){
        $url = 'http://www.omdbapi.com/?apikey=d344f0d6&t='.$title.'&plot='.$plot;
        $response = $this->client->get($url)->getBody()->getContents();

        return $response;
    }
}