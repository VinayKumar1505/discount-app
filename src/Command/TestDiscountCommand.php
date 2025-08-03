<?php

namespace App\Command;

use App\Service\Discount\DiscountContext;
use App\Service\Discount\FixedAmountDiscountStrategy;
use App\Service\Discount\PercentageDiscountStrategy;
use SebastianBergmann\CodeCoverage\Util\Percentage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-discount',
    description: 'Test the percentage discount strategy logic',
)]
class TestDiscountCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // $strategy = new PercentageDiscountStrategy(10);
        // $price= 200.00; // Example original price
        // $finalPrice = $strategy->applyDiscount($price);

        // $io->section('Discount Calculation Test');
        // $io->text([
        //     "Original Price: ₹{$price}",
        //     "Discount Percentage: {$strategy->getLabel()}",
        //     "Final Price after Discount: ₹{$finalPrice}"
        // ]);
        // $context= new DiscountContext(new PercentageDiscountStrategy(15));
        // // $strategy = new PercentageDiscountStrategy(15); // Example discount percentage
        // // $context->setStrategy($strategy);
        // $originalPrice = 200.00; // Example original price
        // $finalPrice = $context->applyDiscount($originalPrice); 
        // $io->section('Discount Calculation Test');
        // $io->text([
        //     "Original Price: ₹{$originalPrice}",
        //     "Discount Percentage: {$context->getLabel()}",
        //     "Final Price after Discount: ₹{$finalPrice}"
        // ]);
        $context = new DiscountContext();
        $context->setStrategy(new FixedAmountDiscountStrategy(100));
        $originalPrice = 500.00; // Example original price
        $finalPrice = $context->applyDiscount($originalPrice);

        $io->section('Discount Calculation Test');
        $io->text([
            "Original Price: ₹{$originalPrice}",
            "Discount Amount: ₹100",
            "Final Price after Discount: ₹{$finalPrice}"
        ]);
        $io->success('Discount applied successfully!');
        $context->setStrategy(new PercentageDiscountStrategy(20));
        $finalPrice = $context->applyDiscount($originalPrice);
        $io->section('Percentage Discount Calculation Test');
        $io->text([
            "Original Price: ₹{$originalPrice}",
            "Discount Percentage: {$context->getLabel()}",
            "Final Price after Discount: ₹{$finalPrice}"
        ]);
        $io->success('Discount applied successfully!');

        return Command::SUCCESS;
    }
}
