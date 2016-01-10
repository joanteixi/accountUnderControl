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

        $entryManager = $this->getContainer()->get('entry.manager');

        $em = $this->getContainer()->get('doctrine')->getManager();
        $finder = new Finder();
        $path = __DIR__ . "/../../../var/data/";
        $finder->files()->in($path);

        foreach ($finder as $file) {
            if ($file->getExtension() == 'xls') {

                $output->writeln('Processing file ' . $file->getFilename());
                $objPHPExcel = \PHPExcel_IOFactory::load($file);
                $worksheet = $objPHPExcel->getSheet(0);
                $totalUpdated = 0;
                foreach ($worksheet->getRowIterator(13) as $row) {
                    $entryExcel = $entryManager->createFromRow($row);
                    if (!$entryManager->existsEntryExcel($entryExcel)) {
                        $entry = $entryManager->createEntryEntity($entryExcel);
                        $em->persist($entry);
                        $totalUpdated++;
                    }

                }
            }
        }
        $em->flush();
        $output->writeln('Created: ' . $totalUpdated);
    }


}
