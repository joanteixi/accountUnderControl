<?php

namespace AppBundle\Command;

use AppBundle\Entity\Entry;
use AppBundle\Entity\EntryExcel;
use AppBundle\Manager\EntryManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class DataImportExcelCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('data:import:excel')
            ->setDescription('...')
            ->addArgument('argument', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $argument = $input->getArgument('argument');

        if ($input->getOption('option')) {
            // ...
        }

        //extract all files from a folder

//        $extractor->extractFromPath($path);



        $em = $this->getContainer()->get('doctrine')->getManager();
        $finder = new Finder();
        $path = __DIR__ . "/../../../var/data/";
        $finder->files()->in($path);

        $extractor = $this->getContainer()->get('excel.extractor.catalunyacaixa');
        $entryExcels = $extractor->extractFromPath($finder);

        $entryExcelManager = $this->getContainer()->get('entry.excel.manager');
        $totalUpdated = 0;
        foreach ($entryExcels as $entryExcel) {
            if (!$entryExcelManager->existsEntryExcel($entryExcel)) {
                $totalUpdated++;
                $entry = $entryExcelManager->createEntryEntity($entryExcel);
                $em->persist($entry);
            }
        }

        $em->flush();
        $output->writeln('Created: ' . $totalUpdated);
    }


}
