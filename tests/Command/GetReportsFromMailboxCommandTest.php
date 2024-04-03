<?php
namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

use SecIT\ImapBundle\Connection\Connection;
use App\Command\GetReportsFromMailboxCommand;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;

class GetReportsFromMailboxCommandTest extends KernelTestCase
{


    public function testExecute(): void
    {
        $mockEm = $this->getMockBuilder(EntityManagerInterface::class)->disableOriginalConstructor()->getMock();
        $mockConnection = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $mockParameterBag = $this->getMockBuilder(ParameterBagInterface::class)->disableOriginalConstructor()->getMock();

        self::bootKernel();
        $application = new Application(self::$kernel);

        $application->add(new GetReportsFromMailboxCommand($mockEm, $mockConnection, $mockParameterBag));

        $command = $application->find('app:getreportsfrommailbox');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('[OK] Mailbox processed successfully', $output);

        // ...
    }
}